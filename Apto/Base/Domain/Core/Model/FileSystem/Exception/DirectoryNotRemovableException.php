<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\Exception;

class DirectoryNotRemovableException extends \Exception
{

    /**
     * DirectoryNotRemovableException constructor.
     * @param string $path
     * @param string $message
     */
    public function __construct(string $path, string $message = '')
    {
        parent::__construct('The directory "' . $path . '" could not be removed.' . ($message ? ' ' . $message : ''));
    }

}