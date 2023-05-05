<?php

namespace Apto\Base\Application\Core;

interface UploadMessage
{
    /**
     * @param array $files
     */
    public function setFiles(array $files);

    /**
     * @return array
     */
    public function getFiles(): array;
}