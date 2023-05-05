<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\File;

class FileNameInvalidException extends \Exception
{

    /**
     * FileNameInvalidException constructor.
     * @param string $filename
     */
    public function __construct($filename)
    {
        parent::__construct('The filename "' . $filename . '" is invalid and must not be empty.');
    }

}