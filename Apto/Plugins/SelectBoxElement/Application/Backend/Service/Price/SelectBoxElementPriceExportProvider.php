<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Backend\Service\Price\AbstractPriceExportProvider;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Plugins\SelectBoxElement\Application\Core\Query\SelectBoxItem\SelectBoxItemFinder;

class SelectBoxElementPriceExportProvider extends AbstractPriceExportProvider
{
    const PRICE_TYPE = 'SelectBoxElement';

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * @var SelectBoxItemFinder
     */
    private $selectBoxItemFinder;

    /**
     * SelectBoxElementPriceExportProvider constructor.
     * @param ProductFinder $productFinder
     * @param SelectBoxItemFinder $selectBoxItemFinder
     * @throws PriceTypeNotSetException
     */
    public function __construct(ProductFinder $productFinder, SelectBoxItemFinder $selectBoxItemFinder)
    {
        parent::__construct();
        $this->productFinder = $productFinder;
        $this->selectBoxItemFinder = $selectBoxItemFinder;
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
                    if ($this->getSelectBoxElement($element)) {
                        $selectBoxItems = $this->selectBoxItemFinder->findByElementId($element['id'])['data'];
                        foreach ($selectBoxItems as $key => $selectBoxItem) {
                            $itemPrices = $this->selectBoxItemFinder->findPrices($selectBoxItem['id']);
                            $prices = array_merge($prices, $this->getPriceItems($itemPrices, $product, $section, $element, $selectBoxItem, $key));
                        }
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
    private function getSelectBoxElement(array $element)
    {
        $elementDefinitionArray = json_decode($element['definition'], true);
        if ($elementDefinitionArray['class'] === 'Apto\Plugins\SelectBoxElement\Domain\Core\Model\Product\Element\SelectBoxElementDefinition') {
            return $elementDefinitionArray['json'];
        }
        return false;
    }

    /**
     * @param array $elementPrices
     * @param array $product
     * @param array $section
     * @param array $element
     * @param array $selectBoxItem
     * @param int $key
     * @return array
     * @throws InvalidUuidException
     */
    private function getPriceItems(array $elementPrices, array $product, array $section, array $element, array $selectBoxItem, int $key)
    {
        $prices = [];
        $path = $product['identifier'] . '_' . $section['identifier'] . '_' . $element['identifier'] . '_SelectBoxItem_' . $key;
        foreach ($elementPrices as $aptoPrice) {
            $prices[] = $this->getPriceItem(
                $path,
                $selectBoxItem['id'],
                $aptoPrice['id'],
                $aptoPrice['amount'],
                $aptoPrice['currencyCode'],
                $aptoPrice['customerGroupId']
            );
        }
        return $prices;
    }
}