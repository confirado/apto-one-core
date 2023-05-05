<?php

namespace Apto\Base\Domain\Core\Model\MediaFile;

class MediaFileAlreadyExistsException extends \Exception
{

    /**
     * MediaFileAlreadyExistsException constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct('A MediaFile object for file \'' . $path . '\' already exists.');
    }

}