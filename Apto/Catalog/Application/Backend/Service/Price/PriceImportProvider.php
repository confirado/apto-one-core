<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

interface PriceImportProvider
{
    /**
     * @param PriceItem $priceItem
     */
    public function setPrice(PriceItem $priceItem): void;

    /**
     * @return string
     */
    public function getType(): string;
}