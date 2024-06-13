<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Material;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Catalog\Application\Core\Query\Product\Condition\ProductConditionSetFinder;

class MaterialQueryHandler implements QueryHandlerInterface
{
    /**
     * @var MaterialFinder
     */
    protected $materialFinder;

    /**
     * @param MaterialFinder            $materialFinder
     * @param ProductConditionSetFinder $productConditionSetFinder
     */
    public function __construct(MaterialFinder $materialFinder, ProductConditionSetFinder $productConditionSetFinder)
    {
        $this->materialFinder = $materialFinder;
        $this->productConditionSetFinder = $productConditionSetFinder;
    }

    /**
     * @param FindMaterials $query
     * @return array
     */
    public function handleFindMaterials(FindMaterials $query): array
    {
        return $this->materialFinder->findMaterials($query->getSearchString());
    }

    /**
     * @param FindMaterialsByPage $query
     * @return array
     */
    public function handleFindMaterialsByPage(FindMaterialsByPage $query): array
    {
        return $this->materialFinder->findByListingPageNumber($query->getPageNumber(), $query->getRecordsPerPage(), $query->getSearchString());
    }

    /**
     * @param FindMaterial $query
     * @return array|null
     */
    public function handleFindMaterial(FindMaterial $query)
    {
        return $this->materialFinder->findById($query->getId());
    }

    /**
     * @param FindMaterialPrices $query
     * @return array|null
     */
    public function handleFindMaterialPrices(FindMaterialPrices $query)
    {
        return $this->materialFinder->findPrices($query->getId());
    }

    /**
     * @param FindMaterialGalleryImages $query
     * @return array
     */
    public function handleFindMaterialGalleryImages(FindMaterialGalleryImages $query): array
    {
        return $this->materialFinder->findGalleryImages($query->getId());
    }

    /**
     * @param FindMaterialProperties $query
     * @return array
     */
    public function handleFindMaterialProperties(FindMaterialProperties $query): array
    {
        return $this->materialFinder->findMaterialProperties($query->getId());
    }

    /**
     * @param FindNotAssignedMaterialProperties $query
     * @return array
     */
    public function handleFindNotAssignedMaterialProperties(FindNotAssignedMaterialProperties $query): array
    {
        return $this->materialFinder->findNotAssignedMaterialProperties($query->getMaterialId(), $query->getSearchString());
    }

    /**
     * @param FindMaterialColorRatings $query
     * @return array
     */
    public function handleFindMaterialColorRatings(FindMaterialColorRatings $query): array
    {
        return $this->materialFinder->findColorRatings($query->getId());
    }

    /**
     * @param FindMaterialRenderImages $query
     * @return array
     */
    public function handleFindMaterialRenderImages(FindMaterialRenderImages $query): array
    {
        return $this->materialFinder->findRenderImages($query->getId());
    }

    /**
     * @param FindMaterialConditionSets $query
     *
     * @return array
     */
    public function handleFindMaterialConditionSets(FindMaterialConditionSets $query): array
    {
        $material = $this->materialFinder->findById($query->getId());

        if (is_null($material)) {
            return [];
        }

        return $this->productConditionSetFinder->findByIds($material['conditionSets']);
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindMaterialConditionSets::class => [
            'method' => 'handleFindMaterialConditionSets',
            'aptoMessageName' => 'FindMaterialConditionSets',
            'bus' => 'query_bus'
        ];

        yield FindMaterialsByPage::class => [
            'method' => 'handleFindMaterialsByPage',
            'aptoMessageName' => 'FindMaterialPickerMaterialsByPage',
            'bus' => 'query_bus'
        ];

        yield FindMaterial::class => [
            'method' => 'handleFindMaterial',
            'aptoMessageName' => 'FindMaterialPickerMaterial',
            'bus' => 'query_bus'
        ];

        yield FindMaterialPrices::class => [
            'method' => 'handleFindMaterialPrices',
            'aptoMessageName' => 'FindMaterialPickerMaterialPrices',
            'bus' => 'query_bus'
        ];

        yield FindMaterialGalleryImages::class => [
            'method' => 'handleFindMaterialGalleryImages',
            'aptoMessageName' => 'FindMaterialPickerMaterialGalleryImages',
            'bus' => 'query_bus'
        ];

        yield FindMaterialProperties::class => [
            'method' => 'handleFindMaterialProperties',
            'aptoMessageName' => 'FindMaterialPickerMaterialProperties',
            'bus' => 'query_bus'
        ];

        yield FindNotAssignedMaterialProperties::class => [
            'method' => 'handleFindNotAssignedMaterialProperties',
            'aptoMessageName' => 'FindMaterialPickerNotAssignedMaterialProperties',
            'bus' => 'query_bus'
        ];

        yield FindMaterialColorRatings::class => [
            'method' => 'handleFindMaterialColorRatings',
            'aptoMessageName' => 'FindMaterialPickerMaterialColorRatings',
            'bus' => 'query_bus'
        ];

        yield FindMaterialRenderImages::class => [
            'method' => 'handleFindMaterialRenderImages',
            'aptoMessageName' => 'FindMaterialPickerMaterialRenderImages',
            'bus' => 'query_bus'
        ];
    }
}
