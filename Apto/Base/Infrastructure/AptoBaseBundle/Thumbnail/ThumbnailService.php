<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Thumbnail;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\FileSystemConnector;

abstract class ThumbnailService
{
    // all possible modes
    const MODE_SHRINK = 0;
    const MODE_CROP = 1;

    // default size
    const DEFAULT_WIDTH = 64;
    const DEFAULT_HEIGHT = 64;

    // max size
    const MAX_WIDTH = 1024;
    const MAX_HEIGHT = 1024;

    // extension for thumbnail
    const DEFAULT_THUMBNAIL_EXTENSION = 'jpg';

    // supported modes
    const SUPPORTED_MODES = [
        self::MODE_SHRINK,
        self::MODE_CROP
    ];

    // supported file extensions
    const SUPPORTED_FILE_EXTENSIONS = [
        'jpg', 'jpeg', 'gif', 'png'
    ];

    // supported file extensions
    const SUPPORTED_THUMBNAIL_EXTENSIONS = [
        'jpg', 'gif', 'png'
    ];


    /**
     * @param File $file
     * @return bool
     */
    public function supportsFile(File $file): bool
    {
        return $this->supportsFileExtension($file->getExtension());
    }

    /**
     * @param string $extension
     * @return bool
     */
    public function supportsFileExtension(string $extension): bool
    {
        return in_array($extension, static::SUPPORTED_FILE_EXTENSIONS);
    }

    /**
     * @param string $extension
     * @return bool
     */
    public function supportsThumbnailExtension(string $extension): bool
    {
        return in_array($extension, static::SUPPORTED_THUMBNAIL_EXTENSIONS);
    }

    /**
     * @param File $sourceFile
     * @param FileSystemConnector $sourceConnector
     * @param int|null $width
     * @param int|null $height
     * @param string $thumbnailExtension
     * @return File
     */
    protected function allocateThumbnailFile(
        File $sourceFile,
        FileSystemConnector $sourceConnector,
        $width,
        $height,
        string $thumbnailExtension
    ): File
    {
        $path = ThumbnailService::allocateThumbnailPath($sourceFile, $sourceConnector, $width, $height, $thumbnailExtension);
        $file = File::createFromPath($path);

        return $file;
    }

    /**
     * @param File $sourceFile
     * @param FileSystemConnector $sourceConnector
     * @param int|null $width
     * @param int|null $height
     * @param string $thumbnailExtension
     * @return string
     */
    public static function allocateThumbnailPath(
        File $sourceFile,
        FileSystemConnector $sourceConnector,
        $width,
        $height,
        string $thumbnailExtension
    ): string
    {
        $width = null == $width ? '' : (int)$width;
        $height = null == $height ? '' : (int)$height;

        // todo: add id of connector to hash
        $path =  $sourceFile->getPath();
        $filename = $path . '_' . $width . 'x' . $height . '.' . $thumbnailExtension;

        return $filename;
    }

    /**
     * @param File $sourceFile
     * @param FileSystemConnector $sourceConnector
     * @param int|null $width
     * @param int|null $height
     * @param int $mode
     * @param string $thumbnailExtension
     * @return File
     */
    abstract public function getThumbnailFile(
        File $sourceFile,
        FileSystemConnector $sourceConnector,
        $width = self::DEFAULT_WIDTH,
        $height = self::DEFAULT_HEIGHT,
        int $mode = self::MODE_SHRINK,
        string $thumbnailExtension = self::DEFAULT_THUMBNAIL_EXTENSION
    ): File;

    /**
     * @param File $sourceFile
     * @param FileSystemConnector $sourceConnector
     * @param int|null $width
     * @param int|null $height
     * @param int $mode
     * @param string $thumbnailExtension
     * @return string
     */
    abstract public function getThumbnailUrl(
        File $sourceFile,
        FileSystemConnector $sourceConnector,
        $width = self::DEFAULT_WIDTH,
        $height = self::DEFAULT_HEIGHT,
        int $mode = self::MODE_SHRINK,
        string $thumbnailExtension = self::DEFAULT_THUMBNAIL_EXTENSION
    ): string;
}