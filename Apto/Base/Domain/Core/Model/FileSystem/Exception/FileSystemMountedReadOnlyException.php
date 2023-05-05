<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\Exception;

class FileSystemMountedReadOnlyException extends \Exception
{

    /**
     * FileSystemMountedReadOnlyException constructor.
     */
    public function __construct()
    {
        parent::__construct('The FileSystemConnector was mounted read-only, but a write access was requested.');
    }

}