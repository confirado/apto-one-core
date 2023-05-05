<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Thumbnail;

class ThumbnailServiceUnsupportedFileExtensionException extends \Exception
{

    /**
     * ThumbnailServiceUnsupportedFileExtensionException constructor.
     * @param string $extension
     */
    public function __construct(string $extension)
    {
        parent::__construct('The file extension \'' . $extension . '\' is not supported by this service.');
    }

}