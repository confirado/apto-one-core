<?php

namespace Apto\Base\Application\Core\Commands;

use Apto\Base\Application\Core\UploadMessage;

abstract class UploadCommand implements UploadMessage
{
    /**
     * @var array
     */
    protected $files;

    /**
     * @param array $files
     */
    public function setFiles(array $files)
    {
        $this->files = $files;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}