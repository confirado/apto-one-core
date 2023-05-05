<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslationTypeNotFoundException;
use Apto\Base\Application\Core\Service\Translation\TranslationItem;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Application\Core\Service\Translation\AbstractTranslationExportProvider;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Material\MaterialFinder;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool\PoolFinder;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\PriceGroup\PriceGroupFinder;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Property\GroupFinder;

class MaterialPickerElementTranslationExportProvider extends AbstractTranslationExportProvider
{
    const TRANSLATION_TYPE = 'MaterialPickerElement';

    /**
     * @var GroupFinder
     */
    private $groupFinder;

    /**
     * @var MaterialFinder
     */
    private $materialFinder;

    /**
     * @var PoolFinder
     */
    private $poolFinder;

    /**
     * @var PriceGroupFinder
     */
    private $priceGroupFinder;

    /**
     * @param GroupFinder $groupFinder
     * @param MaterialFinder $materialFinder
     * @param PoolFinder $poolFinder
     * @param PriceGroupFinder $priceGroupFinder
     * @throws TranslationTypeNotFoundException
     */
    public function __construct(
        GroupFinder $groupFinder,
        MaterialFinder $materialFinder,
        PoolFinder $poolFinder,
        PriceGroupFinder $priceGroupFinder
    ) {
        parent::__construct();
        $this->groupFinder = $groupFinder;
        $this->materialFinder = $materialFinder;
        $this->poolFinder = $poolFinder;
        $this->priceGroupFinder = $priceGroupFinder;
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getTranslatedValues(): array
    {
        // Groups
        $groups = $this->groupFinder->findGroups('')['data'];
        // PriceGroups
        $priceGroups = $this->priceGroupFinder->findPriceGroups('')['data'];
        // Materials
        $materials = $this->materialFinder->findMaterials('')['data'];
        // Pools
        $pools = $this->poolFinder->findPools('')['data'];

        $translationItems = [];
        // process Pools
        foreach ($pools as $i => $pool) {
            $translationItems[] = $this->getTranslationItem('Pool', $i, 'name', $pool['name'], $pool['id']);
        }

        // process Materials
        foreach ($materials as $material) {
            $translationItems[] = $this->getTranslationItem('Material', $material['identifier'], 'name', $material['name'], $material['id']);
            $translationItems[] = $this->getTranslationItem('Material', $material['identifier'], 'description', $material['description'], $material['id']);
        }

        // process PriceGroups
        foreach ($priceGroups as $i => $priceGroup) {
            $translationItems[] = $this->getTranslationItem('PriceGroup', $i, 'name', $priceGroup['name'], $priceGroup['id']);
            $translationItems[] = $this->getTranslationItem('PriceGroup', $i, 'internalName', $priceGroup['internalName'], $priceGroup['id']);
        }

        // process Groups
        foreach ($groups as $i => $group) {
            $properties = $this->groupFinder->findGroupProperties($group['id'], '')['data'];
            $translationItems[] = $this->getTranslationItem('Group', $i, 'name', $group['name'], $group['id']);
            foreach ($properties as $property) {
                $translationItems[] = $this->getTranslationItem('Group_' . $i, 'Property', 'name', $property['name'], $property['id']);
            }
        }

        return $translationItems;
    }

    /**
     * @param $prefix
     * @param $identifier
     * @param $fieldName
     * @param $value
     * @param $id
     * @return TranslationItem
     * @throws InvalidUuidException
     */
    private function getTranslationItem($prefix, $identifier, $fieldName, $value, $id): TranslationItem
    {
        return $this->makeTranslationItem(
            $prefix . '_' . $identifier . '_' . $fieldName,
            $value,
            $id
        );
    }
}