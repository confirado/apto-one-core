<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse;

class SuccessMessageResponse extends MessageResponse
{

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();
        $json['message'] = array_merge($json['message'], [
            'error' => false
        ]);

        return $json;
    }

}