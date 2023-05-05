<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Money\Currency;
use Money\Money;

abstract class AbstractPriceExportProvider extends AbstractPriceProvider implements PriceExportProvider
{
    /**
     * @param string $name
     * @param string $entityId
     * @param string $priceId
     * @param int $amount
     * @param string $currencyCode
     * @param string $customerGroupId
     * @return PriceItem
     * @throws InvalidUuidException
     */
    protected function getPriceItem(string $name, string $entityId, string $priceId, int $amount, string $currencyCode, string $customerGroupId): PriceItem
    {
        return new PriceItem(
            $this->priceType,
            $name,
            new AptoUuid($priceId),
            new Money($amount, new Currency($currencyCode)),
            new AptoUuid($customerGroupId),
            new AptoUuid($entityId)
        );
    }
}