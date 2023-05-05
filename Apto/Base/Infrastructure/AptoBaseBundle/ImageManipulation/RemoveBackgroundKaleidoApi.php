<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\ImageManipulation;

use GuzzleHttp\Client;
use Apto\Base\Application\Core\Service\ImageManipulation\RemoveBackground;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;

// exceptions
use GuzzleHttp\Exception\ClientException;
use Apto\Base\Infrastructure\AptoBaseBundle\ImageManipulation\Exception\APIKeyIsEmptyException;
use Apto\Base\Infrastructure\AptoBaseBundle\ImageManipulation\Exception\AuthFailedException;
use Apto\Base\Infrastructure\AptoBaseBundle\ImageManipulation\Exception\ConfigIsEmptyException;
use Apto\Base\Infrastructure\AptoBaseBundle\ImageManipulation\Exception\UnknownErrorException;
use Apto\Base\Infrastructure\AptoBaseBundle\ImageManipulation\Exception\UnknownForegroundException;
use Apto\Base\Infrastructure\AptoBaseBundle\ImageManipulation\Exception\URLIsEmptyException;

class RemoveBackgroundKaleidoApi implements RemoveBackground
{
    /**
     * @var MediaFileSystemConnector
     */
    private $mediaFileSystem;

    /**
     * @var array
     */
    private $config;

    /**
     * @param MediaFileSystemConnector $mediaFileSystem
     * @param AptoParameterInterface $aptoParameter
     */
    public function __construct(MediaFileSystemConnector $mediaFileSystem, AptoParameterInterface $aptoParameter)
    {
        $this->mediaFileSystem = $mediaFileSystem;
        $imageManipulation = $aptoParameter->has('image_manipulation') ? $aptoParameter->get('image_manipulation') : [];

        $this->config = [];
        if (array_key_exists('kaleido', $imageManipulation)) {
            $this->config = $imageManipulation['kaleido'];
        }
    }

    /**
     * @param File $srcFile
     * @param File $destFile
     * @throws APIKeyIsEmptyException
     * @throws AuthFailedException
     * @throws ConfigIsEmptyException
     * @throws URLIsEmptyException
     * @throws UnknownErrorException
     * @throws UnknownForegroundException
     */
    public function removeBackground(File $srcFile, File $destFile)
    {
        $this->validConfig();

        $client = new Client();

        try {
            $res = $client->post(
                $this->config['url'],
                [
                    'multipart' => [
                        [
                            'name' => 'image_file',
                            'contents' => fopen(
                                $this->mediaFileSystem->getAbsolutePath($srcFile->getPath()),
                                'r'
                            )
                        ],
                        [
                            'name' => 'crop',
                            'contents' => $this->config['crop']
                        ],
                        [
                            'name' => 'crop_margin',
                            'contents' => $this->config['crop_margin']
                        ],
                        [
                            'name' => 'size',
                            'contents' => 'auto'
                        ]
                    ],
                    'headers' => [
                        'X-Api-Key' => $this->config['api_key']
                    ]
                ]
            );
            $fp = fopen($this->mediaFileSystem->getAbsolutePath($destFile->getPath()), "wb");
            fwrite($fp, $res->getBody());
            fclose($fp);

        } catch (ClientException $clientException) {
            $response = json_decode($clientException->getResponse()->getBody()->getContents(), true);

            $i = 0;
            foreach ($response['errors'] as $error) {
                $i++;
                if(array_key_exists('code', $error)) {
                    switch ($error['code']) {
                        case 'auth_failed': {
                            throw new AuthFailedException($error['title']);
                        }
                        case 'unknown_foreground': {
                            throw new UnknownForegroundException($error['title']);
                        }
                    }
                }

                if($i === count($response['errors'])) {
                    throw new UnknownErrorException($error['title']);
                }
            }

            throw new UnknownErrorException('Can not read error message.');
        }
    }

    /**
     * @return void
     * @throws ConfigIsEmptyException
     * @throws URLIsEmptyException
     * @throws APIKeyIsEmptyException
     */
    private function validConfig()
    {
        $config = $this->config;

        if (count($config) === 0) {
            throw new ConfigIsEmptyException('Config is empty.');
        }

        if (!array_key_exists('url', $config) || trim($config['url']) === '') {
            throw new URLIsEmptyException('URL is empty.');
        }

        if (!array_key_exists('api_key', $config) || trim($config['api_key']) === '') {
            throw new APIKeyIsEmptyException('API-Key is empty.');
        }

        if (!array_key_exists('crop', $config) || !$config['crop']) {
            $config['crop'] = false;
        }
    }
}
