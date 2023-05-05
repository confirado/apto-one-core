<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;

class ProductPriceExportProvider extends AbstractPriceExportProvider
{
    const PRICE_TYPE = 'Product';

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * ProductPriceExportProvider constructor.
     * @param ProductFinder $productFinder
     * @throws PriceTypeNotSetException
     */
    public function __construct(ProductFinder $productFinder)
    {
        parent::__construct();
        $this->productFinder = $productFinder;
    }

    /**
     * @param array $productIds
     * @param array $filter
     * @return array
     * @throws InvalidUuidException
     */
    public function getPrices(array $productIds, array $filter): array
    {
        $products = [];
        foreach ($productIds as $productId) {
            $products[] = $this->productFinder->findProductSectionElementPrices($productId);
        }
        $prices = [];
        foreach ($products as $product) {
            if (!array_key_exists('Product', $filter) || true === $filter['Product']) {
                $prices = array_merge($prices, $this->getPricesOfEntity($product));
            }

            foreach ($product['sections'] as $section) {
                if (!array_key_exists('Section', $filter) || true === $filter['Section']) {
                    $prices = array_merge($prices, $this->getPricesOfEntity($section, $product['identifier'] . '_PS_'));
                }

                foreach ($section['elements'] as $element) {
                    if (!array_key_exists('Element', $filter) || true === $filter['Element']) {
                        $prices = array_merge($prices, $this->getPricesOfEntity($element, $product['identifier'] . '_PSE_' . $section['identifier'] . '_'));
                    }
                }
            }
        }
        return $prices;
    }

    /**
     * @param $entity
     * @param string $prefix
     * @return array
     * @throws InvalidUuidException
     */
    private function getPricesOfEntity($entity, string $prefix = '')
    {
        $prices = [];
        foreach ($entity['aptoPrices'] as $aptoPrice) {
            $prices[] = $this->getPriceItem(
                $prefix . $entity['identifier'],
                $entity['id'],
                $aptoPrice['id'],
                $aptoPrice['amount'],
                $aptoPrice['currencyCode'],
                $aptoPrice['customerGroupId']
            );
        }
        return $prices;
    }
}