<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Backend\Service\Price\AbstractPriceExportProvider;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Plugins\PricePerUnitElement\Application\Core\Query\PricePerUnitItem\PricePerUnitItemFinder;

class PricePerUnitPriceExportProvider extends AbstractPriceExportProvider
{
    const PRICE_TYPE = 'PricePerUnit';

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * @var PricePerUnitItemFinder
     */
    private $pricePerUnitItemFinder;

    /**
     * PricePerUnitPriceExportProvider constructor.
     * @param ProductFinder $productFinder
     * @param PricePerUnitItemFinder $pricePerUnitItemFinder
     * @throws PriceTypeNotSetException
     */
    public function __construct(ProductFinder $productFinder, PricePerUnitItemFinder $pricePerUnitItemFinder)
    {
        parent::__construct();
        $this->productFinder = $productFinder;
        $this->pricePerUnitItemFinder = $pricePerUnitItemFinder;
    }

    /**
     * @param array $productIds
     * @param array $filter
     * @return array
     * @throws InvalidUuidException
     */
    public function getPrices(array $productIds, array $filter): array
    {
        $prices = [];
        if (array_key_exists($this->getType(), $filter) && false === $filter[$this->getType()]) {
            return $prices;
        }

        $products = [];
        foreach ($productIds as $productId) {
            $products[] = $this->productFinder->findProductSectionElementPrices($productId);
        }

        foreach ($products as $product) {
            foreach ($product['sections'] as $section) {
                foreach ($section['elements'] as $element) {
                    if ($this->getPricePerUnitElement($element)) {
                        $elementPrices = $this->pricePerUnitItemFinder->findPrices($element['id']);
                        $prices = array_merge($prices, $this->getPriceItems($elementPrices, $product, $section, $element));
                    }
                }
            }
        }
        return $prices;
    }

    /**
     * @param array $element
     * @return bool|mixed
     */
    private function getPricePerUnitElement(array $element)
    {
        $elementDefinitionArray = json_decode($element['definition'], true);
        if ($elementDefinitionArray['class'] === 'Apto\Plugins\PricePerUnitElement\Domain\Core\Model\Product\Element\PricePerUnitElementDefinition') {
            return $elementDefinitionArray['json'];
        }
        return false;
    }

    /**
     * @param array $elementPrices
     * @param array $product
     * @param array $section
     * @param array $element
     * @return array
     * @throws InvalidUuidException
     */
    private function getPriceItems(array $elementPrices, array $product, array $section, array $element)
    {
        $prices = [];
        $path = $product['identifier'] . '_' . $section['identifier'] . '_' . $element['identifier'];
        foreach ($elementPrices as $aptoPrice) {
            $prices[] = $this->getPriceItem(
                $path,
                $element['id'],
                $aptoPrice['id'],
                $aptoPrice['amount'],
                $aptoPrice['currencyCode'],
                $aptoPrice['customerGroupId']
            );
        }
        return $prices;
    }
}