<?php

namespace Apto\Catalog\Application\Core\Query\Product\Section;

use Apto\Base\Application\Core\QueryHandlerInterface;

class SectionQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ProductSectionFinder
     */
    private $productSectionFinder;

    /**
     * SectionQueryHandler constructor.
     * @param ProductSectionFinder $productSectionFinder
     */
    public function __construct(ProductSectionFinder $productSectionFinder)
    {
        $this->productSectionFinder = $productSectionFinder;
    }

    /**
     * @param FindSection $query
     * @return array|null
     */
    public function handleFindSection(FindSection $query)
    {
        return $this->productSectionFinder->findById($query->getSectionId());
    }

    /**
     * @param FindSectionElements $query
     * @return array|null
     */
    public function handleFindSectionElements(FindSectionElements $query)
    {
        return $this->productSectionFinder->findElements($query->getSectionId());
    }

    /**
     * @param FindSectionPrices $query
     * @return array|null
     */
    public function handleFindSectionPrices(FindSectionPrices $query)
    {
        return $this->productSectionFinder->findPrices($query->getSectionId());
    }

    /**
     * @param FindSectionDiscounts $query
     * @return array|null
     */
    public function handleFindSectionDiscounts(FindSectionDiscounts $query)
    {
        return $this->productSectionFinder->findDiscounts($query->getSectionId());
    }

    /**
     * @param FindSectionCustomProperties $query
     * @return array|null
     */
    public function handleFindSectionCustomProperties(FindSectionCustomProperties $query)
    {
        return $this->productSectionFinder->findCustomProperties($query->getSectionId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindSection::class => [
            'method' => 'handleFindSection',
            'bus' => 'query_bus'
        ];

        yield FindSectionElements::class => [
            'method' => 'handleFindSectionElements',
            'bus' => 'query_bus'
        ];

        yield FindSectionPrices::class => [
            'method' => 'handleFindSectionPrices',
            'bus' => 'query_bus'
        ];

        yield FindSectionDiscounts::class => [
            'method' => 'handleFindSectionDiscounts',
            'bus' => 'query_bus'
        ];

        yield FindSectionCustomProperties::class => [
            'method' => 'handleFindSectionCustomProperties',
            'bus' => 'query_bus'
        ];
    }
}