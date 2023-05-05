<?php

namespace Apto\Catalog\Application\Backend\Service\Price\Exceptions;

class PriceTypeNotMatchingException extends \Exception
{
    /**
     * TranslatedTypeNotMatchingException constructor.
     * @param string $givenPriceType
     * @param string $expectedPriceType
     */
    public function __construct(string $givenPriceType, string $expectedPriceType)
    {
        $message = 'Unexpected PriceType: ' . $givenPriceType . '. Expected: ' . $expectedPriceType;
        parent::__construct($message);
    }
}
