<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\Directory;

class DirectoryInvalidCharactersException extends \Exception
{

    /**
     * DirectoryInvalidCharactersException constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct('The directory "' . $path . '" contains invalid characters.');
    }

}