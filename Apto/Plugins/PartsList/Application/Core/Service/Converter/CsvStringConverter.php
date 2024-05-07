<?php

namespace Apto\Plugins\PartsList\Application\Core\Service\Converter;

use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupFinder;
use Apto\Base\Application\Core\Service\SessionStoreInterface;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Application\Core\Service\Csv\Export\CsvExport;
use Apto\Catalog\Application\Core\Service\Formula\NoProductIdGivenException;
use Apto\Catalog\Application\Core\Service\Formula\NoStateGivenException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Plugins\PartsList\Domain\Core\Service\ConfigurationPartsList;
use Money\Currency;

class CsvStringConverter
{
    /**
     * @var ConfigurationPartsList
     */
    private ConfigurationPartsList $configurationPartsList;

    /**
     * @var ComputedProductValueCalculator
     */
    private ComputedProductValueCalculator $computedProductValueCalculator;

    /**
     * @var CustomerGroupFinder
     */
    private CustomerGroupFinder $customerGroupFinder;

    /**
     * @var ProductFinder
     */
    private ProductFinder $productFinder;

    /**
     * @var AptoLocale
     */
    private AptoLocale $locale;

    /**
     * @param SessionStoreInterface $sessionStore
     * @param ConfigurationPartsList $configurationPartsList
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     * @param CustomerGroupFinder $customerGroupFinder
     * @param ProductFinder $productFinder
     */
    public function __construct(
        SessionStoreInterface $sessionStore,
        ConfigurationPartsList $configurationPartsList,
        ComputedProductValueCalculator $computedProductValueCalculator,
        CustomerGroupFinder $customerGroupFinder,
        ProductFinder $productFinder
    ) {
        $this->configurationPartsList = $configurationPartsList;
        $this->computedProductValueCalculator = $computedProductValueCalculator;
        $this->customerGroupFinder = $customerGroupFinder;
        $this->productFinder = $productFinder;
        $this->locale = new AptoLocale($sessionStore->get('_locale', 'de_DE'));
    }

    /**
     * @param AptoUuid $productId
     * @param State $state
     * @param Currency $currency
     * @param string $shopId
     * @param string $customerGroupExternalId
     * @return string
     * @throws CircularReferenceException
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    public function convert(
        AptoUuid $productId,
        State $state,
        Currency $currency,
        string $shopId,
        string $customerGroupExternalId
    ): string {
        $customerGroupId = $this->getCustomerGroupIdByExternalId($shopId, $customerGroupExternalId);
        $fallBackCustomerGroupId = $this->getFallbackCustomerGroupId($customerGroupId);
        $computedValues = $this->computedProductValueCalculator->calculateComputedValues($productId->getId(), $state, true);
        $partsPrice = $this->configurationPartsList->getTotalPrice($productId, $state, $currency, $customerGroupId, $fallBackCustomerGroupId, $computedValues);
        $product = $this->productFinder->findById($productId->getId());
        $articleName = AptoTranslatedValue::fromArray($product['name']);
        $articleNumber = $product['articleNumber'] ?: 'NA';

        $content = [];
        $content = $this->createHeader(
            $content,
            $articleName->getTranslation($this->locale, new AptoLocale('de_DE'), true)->getValue(),
            $articleNumber,
            (float) $partsPrice->getAmount()
        );

        $content = $this->createRows(
            $content,
            $this->configurationPartsList->getBasicList(
                $productId,
                $state,
                $currency,
                $customerGroupId,
                $fallBackCustomerGroupId,
                $this->locale,
                $computedValues
            )
        );

        $csv = new CsvExport($content, ';');
        $csv->createHeader($this->createHeadline());
        return $csv->getCSVString();
    }

    /**
     * @param AptoUuid $productId
     * @param State $state
     * @param Currency $currency
     * @param string $shopId
     * @param string $customerGroupExternalId
     * @return array
     * @throws CircularReferenceException
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    public function getCsvListAsArray(
        AptoUuid $productId,
        State $state,
        Currency $currency,
        string $shopId,
        string $customerGroupExternalId,
        ?string $categoryId
    ): array {
        $customerGroupId = $this->getCustomerGroupIdByExternalId($shopId, $customerGroupExternalId);
        $fallBackCustomerGroupId = $this->getFallbackCustomerGroupId($customerGroupId);
        $computedValues = $this->computedProductValueCalculator->calculateComputedValues($productId->getId(), $state, true);

        return $this->configurationPartsList->getBasicList(
            $productId,
            $state,
            $currency,
            $customerGroupId,
            $fallBackCustomerGroupId,
            $this->locale,
            $computedValues,
            $categoryId
        );
    }

    /**
     * @param AptoUuid $customerGroupId
     * @return AptoUuid
     * @throws InvalidUuidException
     */
    private function getFallbackCustomerGroupId(AptoUuid $customerGroupId): AptoUuid
    {
        $fallBackCustomerGroup = $this->customerGroupFinder->findFallbackCustomerGroup();
        if (null === $fallBackCustomerGroup) {
            return $customerGroupId;
        }

        return new AptoUuid($fallBackCustomerGroup['id']);
    }

    /**
     * @param string $shopId
     * @param string $customerGroupExternalId
     * @return AptoUuid
     * @throws InvalidUuidException
     */
    private function getCustomerGroupIdByExternalId(string $shopId, string $customerGroupExternalId): AptoUuid
    {
        $customerGroup = $this->customerGroupFinder->findByShopAndExternalId($shopId, $customerGroupExternalId);
        if ($customerGroup === null) {
            throw new \InvalidArgumentException('No CustomerGroup found by shopId: "' . $shopId . '" and externalId: "' . $customerGroupExternalId . '".');
        }

        return new AptoUuid($customerGroup['id']);
    }

    /**
     * @param array $content
     * @param string $articleNumber
     * @param string $articleName
     * @param float $totalMaterialCosts
     * @return array
     */
    private function createHeader(
        array $content,
        string $articleNumber,
        string $articleName,
        float $totalMaterialCosts
    ): array {
        $headerInfo = [];

        // line one...
        $headerInfo[0] = 'Artikelnummer';
        $headerInfo[1] = $this->formatNumber($articleNumber);
        $headerInfo[2] = '';
        $headerInfo[3] = '';
        $headerInfo[4] = '';
        $headerInfo[5] = '';
        $headerInfo[6] = '';
        $headerInfo[7] = '';
        $headerInfo[8] = '';
        array_push($content, $headerInfo);
        // line two...
        $headerInfo[0] = '';
        $headerInfo[1] = '';
        $headerInfo[2] = '';
        $headerInfo[3] = '';
        $headerInfo[4] = '';
        $headerInfo[5] = '';
        $headerInfo[6] = 'Summe Materialkosten';
        $headerInfo[7] = '';
        $headerInfo[8] = $this->formatFloatValue($totalMaterialCosts / 100);
        array_push($content, $headerInfo);
        // line two...
        $headerInfo[0] = '';
        $headerInfo[1] = $articleName;
        $headerInfo[2] = '';
        $headerInfo[3] = '';
        $headerInfo[4] = '';
        $headerInfo[5] = '';
        $headerInfo[6] = '';
        $headerInfo[7] = '';
        $headerInfo[8] = '';
        array_push($content, $headerInfo);

        return $content;
    }

    /**
     * @param array $content
     * @param array $rows
     * @return array
     */
    private function createRows(array $content, array $rows): array
    {
        foreach ($rows as $i => $row) {
            $entry = [];
            $entry[0] = $row['partNumber']; // Artikelnr
            $entry[1] = $row['partName']; // Benennung
            $entry[2] = ($i + 1) * 10; // Stl-Pos
            $entry[3] = $this->formatFloatValue($row['quantity']); // Menge
            $entry[4] = $row['unit']; // MES
            $entry[5] = $this->formatFloatValue($row['itemPrice']); // Ver.-Preis
            $entry[6] = $row['baseQuantity']; // Basis
            $entry[7] = $row['unit']; // ME
            $entry[8] = $this->formatFloatValue($row['itemPriceTotal']); // Mat.-Kosten

            array_push($content, $entry);
        }

        return $content;
    }

    /**
     * @return array
     */
    private function createHeadline(): array
    {
        $headline = [];
        $headline['Artikelnr'] = true;
        $headline['Benennung'] = true;
        $headline['Stl-Pos'] = true;
        $headline['Menge'] = true;
        $headline['MES'] = true;
        $headline['Ver.-Preis'] = true;
        $headline['Basis'] = true;
        $headline['ME'] = true;
        $headline['Mat.-Kosten'] = true;

        return $headline;
    }

    /**
     * @param string $value
     * @return string
     */
    private function formatNumber(string $value): string
    {
        //use ' to show prefix 0 if needed
        return "'" . $value;
    }

    /**
     * @param $value
     * @return string
     */
    private function formatFloatValue($value): string
    {
        $value = str_replace(',', '.', $value);
        $value = (string)round((float)$value,2);
        //for now it's ok, if we only replace the dot...
        return str_replace('.', ',', $value);
    }
}
