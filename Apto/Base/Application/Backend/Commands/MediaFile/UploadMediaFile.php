<?php

namespace Apto\Base\Application\Backend\Commands\MediaFile;

use Apto\Base\Application\Core\CommandInterface;
use Apto\Base\Application\Core\Commands\AbstractUploadCommand;

class UploadMediaFile extends AbstractUploadCommand implements CommandInterface
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var bool
     */
    protected $overwriteExisting;

    /**
     * UploadMediaFile constructor.
     * @param string $directory relative path to destination directory inside media root
     * @param bool $overwriteExisting flag if existing file should be overwritten
     */
    public function __construct(string $directory, bool $overwriteExisting = false)
    {
        $this->directory = $directory;
        $this->overwriteExisting = $overwriteExisting;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return bool
     */
    public function getOverwriteExisting(): bool
    {
        return $this->overwriteExisting;
    }
}