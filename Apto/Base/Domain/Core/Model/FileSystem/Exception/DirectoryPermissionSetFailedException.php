<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\Exception;

class DirectoryPermissionSetFailedException extends \Exception
{

    /**
     * DirectoryPermissionSetFailedException constructor.
     * @param string $path
     * @param string $message
     */
    public function __construct(string $path, string $message = '')
    {
        parent::__construct('The permission of directory "' . $path . '" could not be set.' . ($message ? ' ' . $message : ''));
    }

}