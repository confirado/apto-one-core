<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\Exception;

class FilePermissionSetFailedException extends \Exception
{

    /**
     * FilePermissionSetFailedException constructor.
     * @param string $path
     * @param string $message
     */
    public function __construct(string $path, string $message = '')
    {
        parent::__construct('The permission of file "' . $path . '" could not be set.' . ($message ? ' ' . $message : ''));
    }

}