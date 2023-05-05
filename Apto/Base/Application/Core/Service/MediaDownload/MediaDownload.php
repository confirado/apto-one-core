<?php

namespace Apto\Base\Application\Core\Service\MediaDownload;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\RootReadOnlyFileSystemConnector;

use Exception;
use Apto\Base\Application\Core\Service\MediaDownload\Exception\StatusCodeIsNot200Exception;

class MediaDownload
{
    /**
     * @var RootReadOnlyFileSystemConnector
     */
    private $readOnlyFileSystemConnector;

    /**
     * MediaDownload constructor.
     * @param RootReadOnlyFileSystemConnector $readOnlyFileSystemConnector
     */
    public function __construct(RootReadOnlyFileSystemConnector $readOnlyFileSystemConnector)
    {
        $this->readOnlyFileSystemConnector = $readOnlyFileSystemConnector;
    }

    /**
     * @param string $url
     * @param File $destFile
     * @throws StatusCodeIsNot200Exception
     */
    public function downloadImageByURL(string $url, File $destFile)
    {
        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        try {
            $image = file_get_contents($url, false, stream_context_create($arrContextOptions));

            // read status code
            $status_line = $http_response_header[0];
            preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
            $status = $match[1];

            if ($status !== "200") {
                throw new StatusCodeIsNot200Exception('Can not read media. The URL: ' . $url . ' returns the status code: ' . $status );
            }

            // save media in the transfer File.
            $fp = fopen($this->readOnlyFileSystemConnector->getAbsolutePath($destFile->getPath()), "wb");
            fwrite($fp, $image);
            fclose($fp);
        } catch (Exception $exception) {
            preg_match('{HTTP\/\S*\s(\d{3})}', $exception->getMessage(), $match);
            $status = $match[1];
            throw new StatusCodeIsNot200Exception('Can not read media. The URL: ' . $url . ' returns the status code: ' . $status );
        }
    }
}
