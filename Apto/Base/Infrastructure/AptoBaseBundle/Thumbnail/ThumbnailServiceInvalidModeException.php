<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Thumbnail;

class ThumbnailServiceInvalidModeException extends \Exception
{

    /**
     * ThumbnailServiceInvalidModeException constructor.
     * @param string $mode
     */
    public function __construct(string $mode)
    {
        parent::__construct('The mode \'' . $mode . '\' is invalid.');
    }

}