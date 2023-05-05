<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\Exception;

class FileSystemInvalidRootDirectoryException extends \Exception
{

    /**
     * FileSystemInvalidRootDirectoryException constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct('The defined filesystem root directory \'' . $path . '\' does not exist or is not reachable.');
    }

}