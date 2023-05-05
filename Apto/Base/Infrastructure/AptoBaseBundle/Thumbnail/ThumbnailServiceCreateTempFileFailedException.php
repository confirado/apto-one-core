<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Thumbnail;

class ThumbnailServiceCreateTempFileFailedException extends \Exception
{

    /**
     * ThumbnailServiceCreateTempFileFailedException constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        parent::__construct('The tempfile \'' . $file . '\' could not be created.');
    }

}