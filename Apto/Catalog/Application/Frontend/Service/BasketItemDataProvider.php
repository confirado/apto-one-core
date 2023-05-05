<?php

namespace Apto\Catalog\Application\Frontend\Service;

use Money\Currency;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfiguration;

interface BasketItemDataProvider
{
    /**
     * @param array $data
     * @param AptoUuid $shopId
     * @param BasketConfiguration $basketConfiguration
     * @param AptoLocale $locale
     * @param Currency $currency
     * @param Currency $fallbackCurrency
     * @return array
     */
    public function getData(
        array $data,
        AptoUuid $shopId,
        BasketConfiguration $basketConfiguration,
        AptoLocale $locale,
        Currency $currency,
        Currency $fallbackCurrency
    ): array;
}
