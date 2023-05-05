<?php

namespace Apto\Base\Domain\Core\Model\MediaFile;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;

class MediaFile extends AptoAggregate
{
    use AptoCustomProperties;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    protected $md5Hash;

    /**
     * @var int
     */
    protected $size;


    /**
     * File constructor.
     * @param AptoUuid $id
     * @param File $file
     */
    public function __construct(AptoUuid $id, File $file)
    {
        parent::__construct($id);
        $this->publish(
            new MediaFileAdded($id)
        );
        $this->setFile($file);
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return MediaFile
     */
    public function setFile(File $file): MediaFile
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return MediaFile
     */
    public function setSize(int $size): MediaFile
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return string
     */
    public function getMd5Hash(): string
    {
        return $this->md5Hash;
    }

    /**
     * @param string $md5Hash
     * @return MediaFile
     */
    public function setMd5Hash(string $md5Hash): MediaFile
    {
        $this->md5Hash = $md5Hash;
        return $this;
    }
}