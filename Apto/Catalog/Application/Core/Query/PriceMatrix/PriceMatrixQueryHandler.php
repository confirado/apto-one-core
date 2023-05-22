<?php

namespace Apto\Catalog\Application\Core\Query\PriceMatrix;

use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupFinder;
use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Catalog\Application\Core\Query\Shop\ShopFinder;
use Apto\Catalog\Application\Core\Service\Csv\Export\CsvExport;
use Money\Currencies\ISOCurrencies;
use Money\Currency;

class PriceMatrixQueryHandler implements QueryHandlerInterface
{
    /**
     * @var PriceMatrixFinder
     */
    private $priceMatrixFinder;

    /**
     * @var CustomerGroupFinder
     */
    private $customerGroupFinder;

    /**
     * @var ShopFinder
     */
    private $shopFinder;

    /**
     * @var RequestStore
     */
    private $requestStore;

    /**
     * PriceMatrixQueryHandler constructor.
     * @param PriceMatrixFinder $priceMatrixFinder
     * @param CustomerGroupFinder $customerGroupFinder
     * @param ShopFinder $shopFinder
     */
    public function __construct(PriceMatrixFinder $priceMatrixFinder, CustomerGroupFinder $customerGroupFinder, ShopFinder $shopFinder, RequestStore $requestStore)
    {
        $this->priceMatrixFinder = $priceMatrixFinder;
        $this->customerGroupFinder = $customerGroupFinder;
        $this->shopFinder = $shopFinder;
        $this->requestStore = $requestStore;
    }

    /**
     * @param FindPriceMatrix $query
     * @return array
     */
    public function handleFindPriceMatrix(FindPriceMatrix $query): array
    {
        return $this->priceMatrixFinder->findById(
            $query->getId()
        );
    }

    /**
     * @param FindPriceMatrices $query
     * @return array
     */
    public function handleFindPriceMatrices(FindPriceMatrices $query): array
    {
        return $this->priceMatrixFinder->findPriceMatrices(
            $query->getSearchString()
        );
    }

    /**
     * @param FindPriceMatricesByPage $query
     * @return array
     */
    public function handleFindPriceMatricesByPage(FindPriceMatricesByPage $query): array
    {
        return $this->priceMatrixFinder->findByListingPageNumber(
            $query->getPageNumber(),
            $query->getRecordsPerPage(),
            $query->getSearchString()
        );
    }

    /**
     * @param FindPriceMatrixElements $query
     * @return array
     */
    public function handleFindPriceMatrixElements(FindPriceMatrixElements $query): array
    {
        return $this->priceMatrixFinder->findElements(
            $query->getId()
        );
    }

    /**
     * @param FindPriceMatrixElementPrices $query
     * @return array
     */
    public function handleFindPriceMatrixElementPrices(FindPriceMatrixElementPrices $query): array
    {
        return $this->priceMatrixFinder->findElementPrices(
            $query->getId(),
            $query->getElementId()
        );
    }

    /**
     * @param FindPriceMatrixElementCustomProperties $query
     * @return array
     */
    public function handleFindPriceMatrixElementCustomProperties(FindPriceMatrixElementCustomProperties $query): array
    {
        return $this->priceMatrixFinder->findElementCustomProperties(
            $query->getId(),
            $query->getElementId()
        );
    }

    /**
     * @param FindPriceMatrixRules $query
     * @return array
     */
    public function handleFindPriceMatrixRules(FindPriceMatrixRules $query): array
    {
        $shopCurrency = $query->getShopCurrency();
        $shopCurrencyObj = new Currency($shopCurrency['currency']);
        $customerGroupExternalId = $query->getCustomerGroupExternalId();

        // get shop id
        $connector = $this->shopFinder->findConnectorConfigByDomain($this->requestStore->getHttpHost());
        $customerGroup = $this->getCustomerGroup($connector['shopId'], $customerGroupExternalId);

        return $this->priceMatrixFinder->findRules(
            $query->getId(),
            $shopCurrencyObj->getCode(),
            $customerGroup['id'],
            $this->getFallbackCustomerGroupId()
        );
    }

    /**
     * @param FindPriceMatrixLookupTable $query
     * @return array
     */
    public function handleFindPriceMatrixLookupTable(FindPriceMatrixLookupTable $query): array
    {
        $shopCurrency = $query->getShopCurrency();
        $shopCurrencyObj = new Currency($shopCurrency['currency']);
        $customerGroupExternalId = $query->getCustomerGroupExternalId();

        // get shop id
        $connector = $this->shopFinder->findConnectorConfigByDomain($this->requestStore->getHttpHost());
        $customerGroup = $this->getCustomerGroup($connector['shopId'], $customerGroupExternalId);

        return $this->priceMatrixFinder->findMatrixLookupTable(
            $query->getId(),
            $shopCurrencyObj->getCode(),
            $connector['currency'],
            $customerGroup['id'],
            $this->getFallbackCustomerGroupId()
        );
    }

    /**
     * @param GetPriceMatrixCsvString $query
     * @return string|null
     */
    public function handleGetPriceMatrixCsvString(GetPriceMatrixCsvString $query): ?string
    {
        $currencies = new ISOCurrencies();
        $currency = new Currency($query->getCurrencyCode());
        $minorUnit = $currencies->subunitFor($currency);
        $factor = 1;

        for ($i = 0; $i < $minorUnit; $i++) {
            $factor *= 10;
        }

        $elements = $this->priceMatrixFinder->findElements($query->getPriceMatrixId());
        $elements = $elements['elements'];
        $csvArray = [];

        foreach ($elements as $element) {
            foreach ($element['aptoPrices'] as $aptoPrice) {
                if (
                    $aptoPrice["currencyCode"] === $currency->getCode() &&
                    $aptoPrice["customerGroupId"] === $query->getCustomerGroupId()
                ) {
                    $csvArray[] = [
                        'Breite' => $element['columnValue'],
                        'Höhe' => $element['rowValue'],
                        'Preis' => number_format($aptoPrice['amount'] / $factor, $minorUnit,',', '')
                    ];
                }

            }
        }

        // Matrix Type
        if ($query->getCsvType() === 'matrix') {
            return $this->getMatrixCsvString($csvArray);
        }
        // Flat Type (Default)
        else {
            return $this->getFlatCsvString($csvArray);
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield GetPriceMatrixCsvString::class => [
            'method' => 'handleGetPriceMatrixCsvString',
            'bus' => 'query_bus'
        ];

        yield FindPriceMatrixLookupTable::class => [
            'method' => 'handleFindPriceMatrixLookupTable',
            'bus' => 'query_bus'
        ];

        yield FindPriceMatrixRules::class => [
            'method' => 'handleFindPriceMatrixRules',
            'bus' => 'query_bus'
        ];

        yield FindPriceMatrixElementCustomProperties::class => [
            'method' => 'handleFindPriceMatrixElementCustomProperties',
            'bus' => 'query_bus'
        ];

        yield FindPriceMatrixElementPrices::class => [
            'method' => 'handleFindPriceMatrixElementPrices',
            'bus' => 'query_bus'
        ];

        yield FindPriceMatricesByPage::class => [
            'method' => 'handleFindPriceMatricesByPage',
            'bus' => 'query_bus'
        ];

        yield FindPriceMatrixElements::class => [
            'method' => 'handleFindPriceMatrixElements',
            'bus' => 'query_bus'
        ];

        yield FindPriceMatrices::class => [
            'method' => 'handleFindPriceMatrices',
            'bus' => 'query_bus'
        ];

        yield FindPriceMatrix::class => [
            'method' => 'handleFindPriceMatrix',
            'bus' => 'query_bus'
        ];
    }

    /**
     * @param string $shopId
     * @param string|null $customerGroupExternalId
     * @return array
     */
    protected function getCustomerGroup(string $shopId, string $customerGroupExternalId = null): array
    {
        $customerGroup = null;

        // try find customergroup for shop
        if (null !== $customerGroupExternalId) {
            $customerGroup = $this->customerGroupFinder->findByShopAndExternalId(
                $shopId,
                $customerGroupExternalId
            );
        }

        // try find default customer group
        if (null === $customerGroup) {
            $customerGroup = $this->customerGroupFinder->findById('00000000-0000-0000-0000-000000000000');
        }

        // if no customergroup was found create temp customergroup
        if (null === $customerGroup) {
            $customerGroup = [
                'id' => '00000000-0000-0000-0000-000000000000',
                'showGross' => true,
                'inputGross' => true,
                'fallback' => false
            ];
        }

        return $customerGroup;
    }

    /**
     * @return array|null
     */
    protected function getFallbackCustomerGroup()
    {
        return $this->customerGroupFinder->findFallbackCustomerGroup();
    }

    /**
     * @return string|null
     */
    protected function getFallbackCustomerGroupId()
    {
        $fallBackCustomerGroup = $this->getFallbackCustomerGroup();
        return ($fallBackCustomerGroup ? $fallBackCustomerGroup['id'] : null);
    }

    /**
     * @param array $csvArray
     * @return string
     */
    private function getMatrixCsvString(array $csvArray)
    {
        $csvMatrixArray = $this->convertFlatArrayToMatrix($csvArray);
        $csvExport = new CsvExport($csvMatrixArray, ';');
        if (count($csvArray) < 1) {
            $csvExport->createHeader(['Breite' => null, 'Höhe' => null, 'Preis' => null]);
        } else {
            $csvExport->createHeader($csvMatrixArray[0]);
        }

        return $csvExport->getCSVString();
    }

    /**
     * @param array $csvArray
     * @return string
     */
    private function getFlatCsvString(array $csvArray)
    {
        $csvExport = new CsvExport($csvArray, ';');

        if (count($csvArray) < 1) {
            $csvExport->createHeader(['Breite' => null, 'Höhe' => null, 'Preis' => null]);
        } else {
            $csvExport->createHeader($csvArray[0]);
        }

        return $csvExport->getCSVString();
    }

    /**
     * @param array $csvFlatArray
     * @return array
     */
    private function convertFlatArrayToMatrix(array $csvFlatArray)
    {
        $csvMatrixArray = $this->getBaseMatrixArray($csvFlatArray);
        foreach ($csvFlatArray as $value) {
            $csvMatrixArray[$value['Breite']][$value['Höhe']] = $value['Preis'];
        }
        // "flatten" Matrix
        $csvArray = [];
        foreach ($csvMatrixArray as $breite => $column) {
            $i = 0;
            foreach ($column as $hoehe => $value) {
                $csvArray[$i][''] = $hoehe;
                $csvArray[$i][$breite] = $value;
                $i++;
            }
        }
        return $csvArray;
    }

    /**
     * @param array $csvFlatArray
     * @return array
     */
    private function getBaseMatrixArray(array $csvFlatArray)
    {
        $columns = [];
        $rows = [];
        foreach ($csvFlatArray as $item) {
            $columns[] = $item['Breite'];
            $rows[] = $item['Höhe'];
        }
        $columns = array_unique($columns);
        $rows = array_unique($rows);
        sort($columns);
        sort($rows);
        $csvMatrixArray = [];
        foreach ($columns as $column) {
            foreach ($rows as $row) {
                $csvMatrixArray[$column][$row] = '';
            }
        }
        return $csvMatrixArray;
    }
}
