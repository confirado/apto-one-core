<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider;

interface PriceProvider
{
    /**
     * @return string
     */
    public function getElementDefinitionClass(): string;
}