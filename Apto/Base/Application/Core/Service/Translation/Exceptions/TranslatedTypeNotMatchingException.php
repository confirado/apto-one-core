<?php

namespace Apto\Base\Application\Core\Service\Translation\Exceptions;

class TranslatedTypeNotMatchingException extends \Exception
{
    /**
     * TranslatedTypeNotMatchingException constructor.
     * @param string $givenTranslationType
     * @param string $expectedTranslationType
     */
    public function __construct(string $givenTranslationType, string $expectedTranslationType)
    {
        $message = 'Unexpected TranslationType: ' . $givenTranslationType . '. Expected: ' . $expectedTranslationType;
        parent::__construct($message);
    }
}
