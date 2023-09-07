<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Part;

use Money\Currency;
use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemMountedReadOnlyException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\Formula\NoProductIdGivenException;
use Apto\Catalog\Application\Core\Service\Formula\NoStateGivenException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Plugins\PartsList\Application\Core\Service\Converter\CsvFileConverter;

class PartQueryHandler implements QueryHandlerInterface
{
    /**
     * @var PartFinder
     */
    private PartFinder $partFinder;

    /**
     * @var CsvFileConverter
     */
    private CsvFileConverter $csvFileConverter;

    /**
     * @param PartFinder $partFinder
     * @param CsvFileConverter $csvFileConverter
     */
    public function __construct(
        PartFinder $partFinder,
        CsvFileConverter $csvFileConverter
    ) {
        $this->partFinder = $partFinder;
        $this->csvFileConverter = $csvFileConverter;
    }

    /**
     * @param FindPart $query
     * @return array|null
     */
    public function handleFindPart(FindPart $query): ?array
    {
        return $this->partFinder->findById($query->getId());
    }

    /**
     * @param FindPartPrices $query
     * @return array|null
     */
    public function handleFindPartPrices(FindPartPrices $query): ?array
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
    public function handleFindElementUsage(FindElementUsage $query): ?array
    {
        return $this->partFinder->findElementUsageById(
            $query->getId()
        );
    }

    /**
     * @param FindRuleUsage $query
     * @return array|null
     */
    public function handleFindRuleUsage(FindRuleUsage $query): ?array
    {
        return $this->partFinder->findRuleUsageById(
            $query->getId()
        );
    }

    /**
     * @param FindProductUsages $query
     * @return array
     */
    public function handleFindProductUsages(FindProductUsages $query): array
    {
        return $this->partFinder->findProductUsages($query->getId());
    }

    /**
     * @param FindSectionUsages $query
     * @return array
     */
    public function handleFindSectionUsages(FindSectionUsages $query): array
    {
        return $this->partFinder->findSectionUsages($query->getId());
    }

    /**
     * @param FindElementUsages $query
     * @return array
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
     * @throws DirectoryNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     * @throws CircularReferenceException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    public function handleFindPartsListCsv(FindPartsListCsv $query): string
    {
        return $this->csvFileConverter->convert(
            new AptoUuid($query->getProductId()),
            new State($query->getState()),
            new Currency($query->getCurrency()),
            new AptoUuid($query->getCustomerGroupId())
        );
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
