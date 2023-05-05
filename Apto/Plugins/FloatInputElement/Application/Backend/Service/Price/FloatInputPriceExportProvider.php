<?php

namespace Apto\Plugins\FloatInputElement\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Backend\Service\Price\AbstractPriceExportProvider;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Plugins\FloatInputElement\Application\Core\Query\FloatInputItem\FloatInputItemFinder;

class FloatInputPriceExportProvider extends AbstractPriceExportProvider
{
    const PRICE_TYPE = 'FloatInputElement';

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * @var FloatInputItemFinder
     */
    private $floatInputItemFinder;

    /**
     * FloatInputPriceExportProvider constructor.
     * @param ProductFinder $productFinder
     * @param FloatInputItemFinder $floatInputItemFinder
     * @throws PriceTypeNotSetException
     */
    public function __construct(ProductFinder $productFinder, FloatInputItemFinder $floatInputItemFinder)
    {
        parent::__construct();
        $this->productFinder = $productFinder;
        $this->floatInputItemFinder = $floatInputItemFinder;
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
                    if ($this->getFloatInputElement($element)) {
                        $elementPrices = $this->floatInputItemFinder->findPrices($element['id']);
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
    private function getFloatInputElement(array $element)
    {
        $elementDefinitionArray = json_decode($element['definition'], true);
        if ($elementDefinitionArray['class'] === 'Apto\Plugins\FloatInputElement\Domain\Core\Model\Product\Element\FloatInputElementDefinition') {
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