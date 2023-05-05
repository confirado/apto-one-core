<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\File;

class FileInvalidCharactersException extends \Exception
{

    /**
     * FileInvalidCharactersException constructor.
     * @param string $filename
     */
    public function __construct($filename)
    {
        parent::__construct('The filename "' . $filename . '" contains invalid characters.');
    }

}