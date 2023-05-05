<?php

namespace Apto\Catalog\Application\Backend\Service\Price\Exceptions;

class PriceTypeNotFoundException extends \Exception
{
    /**
     * TranslatedTypeNotMatchingException constructor.
     * @param string $givenPriceType
     */
    public function __construct(string $givenPriceType)
    {
        $message = 'No PriceProvider found for type: ' . $givenPriceType;
        parent::__construct($message);
    }
}
