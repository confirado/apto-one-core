<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse;

class DeniedMessageResponse extends ErrorMessageResponse
{

    /**
     * @param string $name
     * @param string $message
     */
    public function __construct(string $name, string $message)
    {
        parent::__construct(
            $name,
            $message,
            0,
            'PermissionError',
            []
        );
    }

}