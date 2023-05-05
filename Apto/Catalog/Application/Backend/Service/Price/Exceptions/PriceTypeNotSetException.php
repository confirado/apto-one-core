<?php

namespace Apto\Catalog\Application\Backend\Service\Price\Exceptions;

class PriceTypeNotSetException extends \Exception
{
    /**
     * @param string $childClass
     */
    public function __construct(string $childClass)
    {
        $message = 'No PriceType set in child class ' . $childClass;
        parent::__construct($message);
    }
}