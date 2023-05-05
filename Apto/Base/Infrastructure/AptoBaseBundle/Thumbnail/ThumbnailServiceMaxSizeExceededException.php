<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Thumbnail;

class ThumbnailServiceMaxSizeExceededException extends \Exception
{

    /**
     * ThumbnailServiceMaxSizeExceededException constructor.
     * @param string $width
     * @param string $height
     */
    public function __construct(string $width, string $height)
    {
        parent::__construct('The size \'' . $width . 'x' . $height . '\' exceeded the allowed size.');
    }

}