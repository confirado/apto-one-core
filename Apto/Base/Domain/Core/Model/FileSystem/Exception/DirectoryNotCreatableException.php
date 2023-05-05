<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\Exception;

class DirectoryNotCreatableException extends \Exception
{

    /**
     * DirectoryNotCreatableException constructor.
     * @param string $path
     * @param string $message
     */
    public function __construct(string $path, string $message = '')
    {
        parent::__construct('The directory "' . $path . '" could not be created.' . ($message ? ' ' . $message : ''));
    }

}