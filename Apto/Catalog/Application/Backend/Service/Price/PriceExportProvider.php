<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

interface PriceExportProvider
{
    /**
     * @param array $productIds
     * @param array $filter
     * @return array[PriceItem]
     */
    public function getPrices(array $productIds, array $filter): array;

    /**
     * @return string
     */
    public function getType(): string;
}