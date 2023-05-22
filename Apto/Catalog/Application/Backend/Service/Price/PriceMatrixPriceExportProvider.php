<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Application\Core\Query\PriceMatrix\PriceMatrixFinder;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Money\Currency;
use Money\Money;

class PriceMatrixPriceExportProvider extends AbstractPriceExportProvider
{
    const PRICE_TYPE = 'PriceMatrix';

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * @var PriceMatrixFinder
     */
    private $priceMatrixFinder;

    /**
     * PriceMatrixPriceExportProvider constructor.
     * @param ProductFinder $productFinder
     * @param PriceMatrixFinder $priceMatrixFinder
     * @throws PriceTypeNotSetException
     */
    public function __construct(ProductFinder $productFinder, PriceMatrixFinder $priceMatrixFinder)
    {
        parent::__construct();
        $this->productFinder = $productFinder;
        $this->priceMatrixFinder = $priceMatrixFinder;
    }

    /**
     * @param array $productIds
     * @param array $filter
     * @return array|PriceItem[]
     * @throws InvalidUuidException
     */
    public function getPrices(array $productIds, array $filter): array
    {
        $priceItems = [];
        if (array_key_exists($this->getType(), $filter) && false === $filter[$this->getType()]) {
            return $priceItems;
        }

        $priceMatrixIds = $this->getPriceMatrixIdsOfProductElements($productIds);

        return $this->getPricesByPriceMatrixIds($priceMatrixIds, $filter);
    }

    /**
     * @param array $priceMatrixIds
     * @param array $filter
     * @return array
     * @throws InvalidUuidException
     */
    public function getPricesByPriceMatrixIds(array $priceMatrixIds, array $filter)
    {
        $priceItems = [];
        if (array_key_exists($this->getType(), $filter) && false === $filter[$this->getType()]) {
            return $priceItems;
        }

        $priceMatrices = $this->priceMatrixFinder->findPriceMatricesByIds($priceMatrixIds)['data'];

        // get All PriceElements of PriceMatrices
        foreach ($priceMatrices as $key => $priceMatrix) {
            $priceMatrices[$key]['name'] = $this->getPriceMatrixName($priceMatrices[$key]['name']);
            $priceMatrices[$key]['elements'] = $this->priceMatrixFinder->findElements($priceMatrices[$key]['id'])['elements'];
        }

        // convert PriceMatrixElements to DTOs (PriceItem)
        foreach ($priceMatrices as $priceMatrix) {
            $priceItems = array_merge($priceItems, $this->getPriceItems($priceMatrix));
        }

        return $priceItems;
    }

    /**
     * @param array $productIds
     * @return array
     */
    public function getMatricesUsedInOtherProducts(array $productIds): array
    {
        $priceMatrixIds = $this->getPriceMatrixIdsOfProductElements($productIds);
        return $this->priceMatricesUsedInOtherProducts($priceMatrixIds, $productIds);
    }

    /**
     * @param array $productIds
     * @return array
     */
    public function getPriceMatrixIdsOfProductElements(array $productIds)
    {
        $priceMatrixIds = [];
        foreach ($productIds as $productId) {
            $sectionsElements = $this->productFinder->findSectionsElements($productId);
            foreach ($sectionsElements['sections'] as $section) {
                foreach ($section['elements'] as $element) {
                    if ($priceMatrixId = $this->getPriceMatrixId($element)) {
                        $priceMatrixIds[] = $priceMatrixId;
                    }
                }
            }
        }
        return array_values(array_unique($priceMatrixIds));
    }

    /**
     * @param array $element
     * @return string|null
     */
    private function getPriceMatrixId(array $element)
    {
        // @todo Implement getPriceMatrixIds in ElementDefinition
        $elementDefinitionArray = json_decode($element['definition'], true);
        if (array_key_exists('priceMatrixId',$elementDefinitionArray['json']) ) {
            return $elementDefinitionArray['json']['priceMatrixId'];
        }
        if (array_key_exists('priceMatrix',$elementDefinitionArray['json']) && $elementDefinitionArray['json']['priceMatrix']['id'] ) {
            return $elementDefinitionArray['json']['priceMatrix']['id'];
        }
        return null;
    }

    /**
     * @param array $priceMatrixIds
     * @param array $productIds
     * @return array
     */
    private function priceMatricesUsedInOtherProducts(array $priceMatrixIds, array $productIds)
    {
        $allProductIds = $this->productFinder->findAllProductIdsByCategories(['searchString' => ''], false);
        $otherProductIds = array_diff($allProductIds, $productIds);

        if (count($otherProductIds) > 0) {
            $otherPriceMatrixIds = $this->getPriceMatrixIdsOfProductElements($otherProductIds);
            $intersectingPriceMatrices = array_intersect($priceMatrixIds, $otherPriceMatrixIds);
            if (count($intersectingPriceMatrices) > 0) {
                return $intersectingPriceMatrices;
            }
        }
        return [];
    }

    /**
     * @param array $priceMatrix
     * @return array
     * @throws InvalidUuidException
     */
    private function getPriceItems(array $priceMatrix)
    {
        $prices = [];
        foreach ($priceMatrix['elements'] as $entry) {
            foreach ($entry['aptoPrices'] as $aptoPrice) {
                $prices[] = $this->getPriceItem(
                    $priceMatrix['name'] . '_' . $entry['columnValue'] . '_' . $entry['rowValue'],
                    $priceMatrix['id'],
                    $aptoPrice['id'],
                    $aptoPrice['amount'],
                    $aptoPrice['currencyCode'],
                    $aptoPrice['customerGroupId']
                );
            }
        }
        return $prices;
    }

    /**
     * @param array $priceMatrixName
     * @return mixed
     */
    private function getPriceMatrixName(array $priceMatrixName)
    {
        foreach ($priceMatrixName as $key => $value) {
            if (!empty($value)) {
                return $value;
            }
        }
    }
}
