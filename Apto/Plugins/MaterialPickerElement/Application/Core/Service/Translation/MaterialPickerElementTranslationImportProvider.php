<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslatedTypeNotMatchingException;
use Apto\Base\Application\Core\Service\Translation\TranslationImportProvider;
use Apto\Base\Application\Core\Service\Translation\TranslationItem;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\MaterialRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\PoolRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroupRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\GroupRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\PropertyRepository;

class MaterialPickerElementTranslationImportProvider implements TranslationImportProvider
{
    const TRANSLATION_TYPE = 'MaterialPickerElement';

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @var PoolRepository
     */
    private $poolRepository;

    /**
     * @var PriceGroupRepository
     */
    private $priceGroupRepository;

    /**
     * @var PropertyRepository
     */
    private $propertyRepository;

    /**
     * @var string
     */
    private $translationType;

    /**
     * @param GroupRepository $groupRepository
     * @param MaterialRepository $materialRepository
     * @param PoolRepository $poolRepository
     * @param PriceGroupRepository $priceGroupRepository
     * @param PropertyRepository $propertyRepository
     */
    public function __construct(
        GroupRepository $groupRepository,
        MaterialRepository $materialRepository,
        PoolRepository $poolRepository,
        PriceGroupRepository $priceGroupRepository,
        PropertyRepository $propertyRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->materialRepository = $materialRepository;
        $this->poolRepository = $poolRepository;
        $this->priceGroupRepository = $priceGroupRepository;
        $this->propertyRepository = $propertyRepository;
        $this->translationType = self::TRANSLATION_TYPE;
    }

    /**
     * @param TranslationItem $translationItem
     * @throws TranslatedTypeNotMatchingException
     * @throws \Exception
     */
    public function setTranslatedValue(TranslationItem $translationItem): void
    {
        if ($translationItem->getTranslationType() !== $this->translationType) {
            throw new TranslatedTypeNotMatchingException($translationItem->getTranslationType(), $this->translationType);
        }

        $fieldName = $translationItem->getFieldName();
        $fieldArray = explode('_', $fieldName);

        switch ($fieldArray[0]) {
            case 'Pool':
                $pool = $this->poolRepository->findById($translationItem->getEntityId()->getId());
                if (null === $pool) {
                    return;
                }

                $pool->setName($pool->getName()->merge($translationItem->getTranslatedValue()));
                $this->poolRepository->update($pool);
                return;
            case 'Material':
                $material = $this->materialRepository->findById($translationItem->getEntityId()->getId());
                if (null === $material) {
                    return;
                }

                if ($fieldArray[2] === 'name') {
                    $material->setName($material->getName()->merge($translationItem->getTranslatedValue()));
                }
                if ($fieldArray[2] === 'description') {
                    $material->setDescription($material->getDescription()->merge($translationItem->getTranslatedValue()));
                }
                $this->materialRepository->update($material);
                return;
            case 'PriceGroup':
                $priceGroup = $this->priceGroupRepository->findById($translationItem->getEntityId()->getId());
                if (null === $priceGroup) {
                    return;
                }

                if ($fieldArray[2] === 'name') {
                    $priceGroup->setName($priceGroup->getName()->merge($translationItem->getTranslatedValue()));
                }
                if ($fieldArray[2] === 'internalName') {
                    $priceGroup->setInternalName($priceGroup->getInternalName()->merge($translationItem->getTranslatedValue()));
                }
                $this->priceGroupRepository->update($priceGroup);
                return;
            case 'Group':
                if ($fieldArray[2] !== 'Property') {
                    $group = $this->groupRepository->findById($translationItem->getEntityId()->getId());
                    if (null === $group) {
                        return;
                    }

                    $group->setName($group->getName()->merge($translationItem->getTranslatedValue()));
                    $this->groupRepository->update($group);
                    return;
                }
                else {
                    $property = $this->propertyRepository->findById($translationItem->getEntityId()->getId());
                    if (null === $property) {
                        return;
                    }

                    $property->setName($property->getName()->merge($translationItem->getTranslatedValue()));
                    $this->propertyRepository->update($property);
                }
                return;
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->translationType;
    }
}