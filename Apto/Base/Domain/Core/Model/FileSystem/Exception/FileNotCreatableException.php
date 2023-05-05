<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\Exception;

class FileNotCreatableException extends \Exception
{

    /**
     * FileNotCreatableException constructor.
     * @param string $path
     * @param string $message
     */
    public function __construct(string $path, string $message = '')
    {
        parent::__construct('The file "' . $path . '" could not be created.' . ($message ? ' ' . $message : ''));
    }

}