<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Thumbnail;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\FileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\RootFileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\ThumbFileSystemConnector;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Base\Infrastructure\AptoBaseBundle\FileSystem\LocalFileSystem;

class ImageMagickThumbnailService extends ThumbnailService
{
    const SUPPORTED_FILE_EXTENSIONS = [
        'jpg', 'jpeg',
        'gif',
        'png',
        'bmp', 'bmp2', 'bmp3', 'wbmp',
        'wmf',
        'ico',
        'tga',
        'tif', 'tiff',
        'svg',
        'pdf',
        'eps', 'eps2', 'eps3',
        'ps', 'ps2', 'ps3',
        'svg',
        'psd',
        'avi',
        'mpg', 'mpeg'
    ];


    /**
     * @var FileSystemConnector
     */
    protected $thumbsConnector;

    /**
     * @var FileSystemConnector
     */
    protected $rootConnector;

    /**
     * @var string
     */
    protected $convertPath;


    /**
     * ThumbnailService constructor.
     * @param ThumbFileSystemConnector $thumbConnector
     * @param RootFileSystemConnector $rootConnector
     * @param string $convertPath
     */
    public function __construct(ThumbFileSystemConnector $thumbConnector, RootFileSystemConnector $rootConnector, AptoParameterInterface $aptoParameter)
    {
        if (!($rootConnector instanceof LocalFileSystem)) {
            throw new \InvalidArgumentException('The given root connector must be an instance of LocalFileSystem.');
        }
        if ($rootConnector->isReadOnly()) {
            throw new \InvalidArgumentException('The given root connector must not be mounted read-only.');
        }
        if (empty($aptoParameter->get('image_magick_path'))) {
            throw new \InvalidArgumentException('The given path to the ImageMagick/GraphicsMagick convert command must not be empty.');
        }

        $this->thumbsConnector = $thumbConnector;
        $this->rootConnector = $rootConnector;
        $this->convertPath = $aptoParameter->get('image_magick_path');
    }

    /**
     * @param File $sourceFile
     * @param FileSystemConnector $sourceConnector
     * @param int|null $width
     * @param int|null $height
     * @param int $mode
     * @param string $thumbnailExtension
     * @return File
     * @throws ThumbnailServiceInvalidModeException
     * @throws ThumbnailServiceMaxSizeExceededException
     * @throws ThumbnailServiceUnsupportedFileExtensionException
     */
    public function getThumbnailFile(
        File $sourceFile,
        FileSystemConnector $sourceConnector,
        $width = self::DEFAULT_WIDTH,
        $height = self::DEFAULT_HEIGHT,
        int $mode = self::MODE_SHRINK,
        string $thumbnailExtension = self::DEFAULT_THUMBNAIL_EXTENSION
    ): File
    {
        if (!$this->supportsFile($sourceFile)) {
            throw new ThumbnailServiceUnsupportedFileExtensionException($sourceFile->getExtension());
        }

        if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
            throw new ThumbnailServiceMaxSizeExceededException($width, $height);
        }

        if (!in_array($mode, self::SUPPORTED_MODES)) {
            throw new ThumbnailServiceInvalidModeException($mode);
        }

        $thumbnailFile = $this->allocateThumbnailFile($sourceFile, $sourceConnector, $width, $height, $thumbnailExtension);
        if (!$this->thumbsConnector->existsFile($thumbnailFile)) {
            $this->createThumbnail($thumbnailFile, $sourceFile, $sourceConnector, $width, $height, $mode, $thumbnailExtension);
        }

        return $thumbnailFile;
    }

    /**
     * @param File $sourceFile
     * @param FileSystemConnector $sourceConnector
     * @param int|null $width
     * @param int|null $height
     * @param int $mode
     * @param string $thumbnailExtension
     * @return string
     */
    public function getThumbnailUrl(
        File $sourceFile,
        FileSystemConnector $sourceConnector,
        $width = self::DEFAULT_WIDTH,
        $height = self::DEFAULT_HEIGHT,
        int $mode = self::MODE_SHRINK,
        string $thumbnailExtension = self::DEFAULT_THUMBNAIL_EXTENSION
    ): string
    {
        $file = $this->getThumbnailFile($sourceFile, $sourceConnector, $width, $height, $mode, $thumbnailExtension);
        $url = $this->thumbsConnector->getFileUrl($file);

        return $url;
    }

    /**
     * @param File $thumbnailFile
     * @param File $sourceFile
     * @param FileSystemConnector $sourceConnector
     * @param int|null $width
     * @param int|null $height
     * @param int $mode
     * @param string $thumbnailExtension
     */
    protected function createThumbnail(
        File $thumbnailFile,
        File $sourceFile,
        FileSystemConnector $sourceConnector,
        $width = null,
        $height = null,
        int $mode,
        string $thumbnailExtension
    ) {
        // get content to local temp file
        $tempFile = $this->allocateLocalTempFile();
        $this->rootConnector->createFile($tempFile, $sourceFile, $sourceConnector, true);
        $this->rootConnector->setFilePermission($tempFile, 0700);

        // do conversion
        switch ($mode) {
            case self::MODE_SHRINK:
                $this->shrinkImage($tempFile, $width, $height, $thumbnailExtension);
                break;
            case self::MODE_CROP:
                $this->cropImage($tempFile, $width, $height, $thumbnailExtension);
                break;
        }

        // create target directory, if not already existing
        if (!$this->thumbsConnector->existsDirectory($thumbnailFile->getDirectory())) {
            $this->thumbsConnector->createDirectory($thumbnailFile->getDirectory(), true);
        }

        // put temp thumbnail file to media
        $this->thumbsConnector->createFile($thumbnailFile, $tempFile, $this->rootConnector, true);

        // remove temp file
        $this->rootConnector->removeFile($tempFile);
    }

    /**
     * @return File
     */
    protected function allocateLocalTempFile(): File
    {
        // hint: windows uses the first 3 chars only...
        $path = tempnam(sys_get_temp_dir(), 'apto_thumb_');
        $file = File::createFromPath($path);

        return $file;
    }

    /**
     * @param File $tempFile
     * @param int|null $width
     * @param int|null $height
     * @param $thumbnailExtension
     */
    protected function shrinkImage(
        File $tempFile,
        $width,
        $height,
        string $thumbnailExtension
    ) {
        $specialParams = '';
        if ($thumbnailExtension === 'jpg') {
            $specialParams = '-background white -alpha remove ';
        }

        $parameters = '{source} -strip ' . $specialParams . '-thumbnail {width}x{height} {destination}';
        $this->executeCommand($parameters, $tempFile, $width, $height, $thumbnailExtension);
    }

    /**
     * @param File $tempFile
     * @param int|null  $width
     * @param int|null  $height
     * @param string $thumbnailExtension
     */
    protected function cropImage(
        File $tempFile,
        $width,
        $height,
        string $thumbnailExtension
    ) {
        $specialParams = '';
        if ($thumbnailExtension === 'jpg') {
            $specialParams = '-background white -alpha remove ';
        }

        $parameters = '{source} -strip ' . $specialParams . '-sample -resize {width}x{height}^ -gravity center -extent {width}x{height} {destination}';
        $this->executeCommand($parameters, $tempFile, $width, $height, $thumbnailExtension);
    }

    /**
     * @param string $parameters
     * @param File $tempFile
     * @param int|null $width
     * @param int|null $height
     * @param string $thumbnailExtension
     */
    protected function executeCommand(
        string $parameters,
        File $tempFile,
        $width,
        $height,
        string $thumbnailExtension
    ) {
        $parameterValues = [
            'source' => escapeshellarg($this->rootConnector->getAbsolutePath($tempFile->getPath()) . '[0]'),
            'destination' => escapeshellarg($thumbnailExtension . ':' . $this->rootConnector->getAbsolutePath($tempFile->getPath())),
            'width' => null == $width ? '' : (int)$width,
            'height' => null == $height ? '' : (int)$height
        ];

        foreach ($parameterValues as $search => $replace) {
            $parameters = str_replace('{' . $search . '}', $replace, $parameters);
        }

        $command = $this->convertPath . ' ' . $parameters;
        exec($command);
    }
}
