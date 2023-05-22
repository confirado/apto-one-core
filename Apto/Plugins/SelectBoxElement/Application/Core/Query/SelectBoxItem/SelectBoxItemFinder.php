<?php

namespace Apto\Plugins\SelectBoxElement\Application\Core\Query\SelectBoxItem;

use Apto\Base\Application\Core\Query\AptoFinder;

interface SelectBoxItemFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $elementId
     * @return array
     */
    public function findByElementId(string $elementId);

    /**
     * @param string $id
     * @return array
     */
    public function findPrices(string $id): array;

    /**
     * @param string $elementId
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @return array
     */
    public function findPrice(string $elementId, string $customerGroupId, string $fallbackCustomerGroupId = null, string $currencyCode, string $fallbackCurrencyCode): array;
}
