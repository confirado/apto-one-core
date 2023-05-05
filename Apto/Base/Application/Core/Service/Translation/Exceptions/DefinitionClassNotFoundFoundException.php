<?php

namespace Apto\Base\Application\Core\Service\Translation\Exceptions;

class DefinitionClassNotFoundFoundException extends \Exception
{
    /**
     * TranslatedTypeNotMatchingException constructor.
     * @param string $givenTranslationType
     */
    public function __construct(string $givenTranslationType)
    {
        $message = 'No TranslationProvider found for type: ' . $givenTranslationType;
        parent::__construct($message);
    }
}
