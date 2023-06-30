<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Part;

use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupFinder;
use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Application\Core\Service\Csv\Export\CsvExport;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Plugins\PartsList\Domain\Core\Service\ConfigurationPartsList;
use Money\Currency;

class PartQueryHandler implements QueryHandlerInterface
{
    /**
     * @var PartFinder
     */
    private $partFinder;

    /**
     * @var MediaFileSystemConnector
     */
    private $mediaConnector;

    /**
     * @var ConfigurationPartsList
     */
    private $configurationPartsList;

    /**
     * @var CustomerGroupFinder
     */
    private $customerGroupFinder;

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * @var ComputedProductValueCalculator
     */
    private $computedProductValueCalculator;

    /**
     * @var RequestStore
     */
    private $requestStore;

    /**
     * @param PartFinder $partFinder
     * @param MediaFileSystemConnector $mediaConnector
     * @param ConfigurationPartsList $configurationPartsList
     * @param CustomerGroupFinder $customerGroupFinder
     * @param ProductFinder $productFinder
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     */
    public function __construct(
        PartFinder $partFinder,
        MediaFileSystemConnector $mediaConnector,
        ConfigurationPartsList $configurationPartsList,
        CustomerGroupFinder $customerGroupFinder,
        ProductFinder $productFinder,
        ComputedProductValueCalculator $computedProductValueCalculator,
        RequestStore $requestStore
    ) {
        $this->partFinder = $partFinder;
        $this->mediaConnector = $mediaConnector;
        $this->configurationPartsList = $configurationPartsList;
        $this->customerGroupFinder = $customerGroupFinder;
        $this->productFinder = $productFinder;
        $this->computedProductValueCalculator = $computedProductValueCalculator;
        $this->requestStore = $requestStore;
    }

    /**
     * @param FindPart $query
     * @return array|null
     */
    public function handleFindPart(FindPart $query)
    {
        return $this->partFinder->findById($query->getId());
    }

    /**
     * @param FindPartPrices $query
     * @return array|null
     */
    public function handleFindPartPrices(FindPartPrices $query)
    {
        return $this->partFinder->findPrices($query->getId());
    }

    /**
     * @param FindParts $query
     * @return array
     */
    public function handleFindParts(FindParts $query): array
    {
        return $this->partFinder->findByListingPageNumber(
            $query->getPageNumber(),
            $query->getRecordsPerPage(),
            $query->getSearchString()
        );
    }

    /**
     * @param FindProducts $query
     * @return array
     */
    public function handleFindProducts(FindProducts $query): array
    {
        return $this->partFinder->findProducts(
            $query->getSearchString()
        );
    }

    /**
     * @param FindProductsSectionsElements $query
     * @return array
     */
    public function handleFindProductsSectionsElements(FindProductsSectionsElements $query): array
    {
        return $this->partFinder->findProductsSectionsElements();
    }

    /**
     * @param FindSections $query
     * @return array
     */
    public function handleFindSections(FindSections $query): array
    {
        return $this->partFinder->findSections(
            $query->getSearchString()
        );
    }

    /**
     * @param FindElements $query
     * @return array
     */
    public function handleFindElements(FindElements $query): array
    {
        return $this->partFinder->findElements(
            $query->getSearchString()
        );
    }

    /**
     * @param FindElementUsage $query
     * @return array|null
     */
    public function handleFindElementUsage(FindElementUsage $query)
    {
        return $this->partFinder->findElementUsageById(
            $query->getId()
        );
    }

    /**
     * @param FindRuleUsage $query
     * @return array|null
     */
    public function handleFindRuleUsage(FindRuleUsage $query)
    {
        return $this->partFinder->findRuleUsageById(
            $query->getId()
        );
    }

    /**
     * @param FindProductUsages $query
     * @return array|null
     */
    public function handleFindProductUsages(FindProductUsages $query): array
    {
        return $this->partFinder->findProductUsages($query->getId());
    }

    /**
     * @param FindSectionUsages $query
     * @return array|null
     */
    public function handleFindSectionUsages(FindSectionUsages $query): array
    {
        return $this->partFinder->findSectionUsages($query->getId());
    }

    /**
     * @param FindElementUsages $query
     * @return array|null
     */
    public function handleFindElementUsages(FindElementUsages $query): array
    {
        return $this->partFinder->findElementUsages($query->getId());
    }

    /**
     * @param FindRuleUsages $query
     * @return array
     */
    public function handleFindRuleUsages(FindRuleUsages $query): array
    {
        return $this->partFinder->findRuleUsages($query->getId());
    }

    /**
     * @param FindPartsListCsv $query
     * @return string
     * @throws FileNotCreatableException
     * @throws InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\Formula\NoProductIdGivenException
     * @throws \Apto\Catalog\Application\Core\Service\Formula\NoStateGivenException
     */
    public function handleFindPartsListCsv(FindPartsListCsv $query)
    {
        $locale = new AptoLocale($query->getLocale());
        $product = $this->productFinder->findById($query->getProductId());
        $articleName = $product['name'][$query->getLocale()];
        $articleNumber = $product['articleNumber'];
        if (null === $articleNumber) {
            $articleNumber = "NA";
        }
        $state = new State($query->getState());
        $productId = new AptoUuid($query->getProductId());
        return $this->makeCsv(
            $productId,
            $state,
            $query->getCustomerGroupId(),
            $articleNumber,
            $articleName,
            $query->getFilename(),
            $locale,
            $query->getCurrency()
        );
    }

    /**
     * @param AptoUuid $productId
     * @param State $state
     * @param string $customerGroupId
     * @param string $articleNumber
     * @param string $articleName
     * @param string $filename
     * @param AptoLocale $locale
     * @param string $currency
     * @return string
     * @throws FileNotCreatableException
     * @throws InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException
     * @throws \Apto\Catalog\Application\Core\Service\Formula\NoProductIdGivenException
     * @throws \Apto\Catalog\Application\Core\Service\Formula\NoStateGivenException
     */
    private function makeCsv(
        AptoUuid $productId,
        State $state,
        string $customerGroupId,
        string $articleNumber,
        string $articleName,
        string $filename,
        AptoLocale $locale,
        string $currency
    ) {
        $currency = new Currency($currency);
        $fallBackCustomerGroupId = $this->customerGroupFinder->findFallbackCustomerGroup()['id'];
        $computedValues = $this->computedProductValueCalculator->calculateComputedValues($productId, $state, true);
        $partsPrice = $this->configurationPartsList->getTotalPrice($productId, $state, $currency, $customerGroupId, $fallBackCustomerGroupId, $computedValues);
        $content = [];
        $content = $this->createHeader(
            $content,
            $articleNumber,
            $articleName,
            $partsPrice->getAmount()
        );

        $content = $this->createRows(
            $content,
            $this->configurationPartsList->getBasicList(
                $productId,
                $state,
                $currency,
                $customerGroupId,
                $customerGroupId,
                $locale,
                $computedValues
            )
        );

        $csv = new CsvExport($content, ';');
        $csv->createHeader($this->createHeadline());

        // create Folders and File
        $folderStructure = $this->generateFolderStructure();
        $csvFile = new File($folderStructure, $filename . '.csv');
        $this->mediaConnector->createFile($csvFile, null, null, true);
        file_put_contents($this->mediaConnector->getAbsolutePath($csvFile->getPath()), $csv->getCSVString());

        return $this->requestStore->getSchemeAndHttpHost() . $this->mediaConnector->getFileUrl($csvFile);
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
        $headerInfo[8] = $this->formatFloatValue($totalMaterialCosts/100);
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
     * @param array $rows
     * @return float
     */
    private function calculateTotalMaterialCosts(array $rows): float
    {
        $result = 0;

        foreach ($rows as $row) {
            $result += $row[8];
        }

        return $result;
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
     * @return Directory
     */
    private function generateFolderStructure(): Directory
    {
        $directory = new Directory('/files/partsList/' . date('Y') . '/' . date('m'));

        // create directory if not already exists
        if (!$this->mediaConnector->existsDirectory($directory)) {
            $this->mediaConnector->createDirectory($directory, true);
        }

        return $directory;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindPart::class => [
            'method' => 'handleFindPart',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindPartPrices::class => [
            'method' => 'handleFindPartPrices',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindParts::class => [
            'method' => 'handleFindParts',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindProducts::class => [
            'method' => 'handleFindProducts',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindProductsSectionsElements::class => [
            'method' => 'handleFindProductsSectionsElements',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindSections::class => [
            'method' => 'handleFindSections',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindElements::class => [
            'method' => 'handleFindElements',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindElementUsage::class => [
            'method' => 'handleFindElementUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindRuleUsage::class => [
            'method' => 'handleFindRuleUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindProductUsages::class => [
            'method' => 'handleFindProductUsages',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindSectionUsages::class => [
            'method' => 'handleFindSectionUsages',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindElementUsages::class => [
            'method' => 'handleFindElementUsages',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindRuleUsages::class => [
            'method' => 'handleFindRuleUsages',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindPartsListCsv::class => [
            'method' => 'handleFindPartsListCsv',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];
    }
}