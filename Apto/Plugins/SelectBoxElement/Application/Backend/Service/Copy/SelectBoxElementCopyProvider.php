<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Service\Copy;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Backend\Service\Product\ProductElementCopyProvider;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItem;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItemRepository;

class SelectBoxElementCopyProvider implements ProductElementCopyProvider
{
    /**
     * @var SelectBoxItemRepository
     */
    private $selectBoxItemRepository;

    /**
     * @var string
     */
    private $priceType;

    /**
     * SelectBoxElementCopyProvider constructor.
     * @param SelectBoxItemRepository $selectBoxItemRepository
     * @param string $priceType
     */
    public function __construct(SelectBoxItemRepository $selectBoxItemRepository, string $priceType)
    {
        $this->selectBoxItemRepository = $selectBoxItemRepository;
        $this->priceType = $priceType;
    }

    public function copy(AptoUuid $oldElementId, AptoUuid $productId, AptoUuid $sectionId, AptoUuid $elementId): void
    {

        $selectBoxItems = $this->selectBoxItemRepository->findByElementId($oldElementId->getId());

        // copy all selectBoxItems
        foreach ($selectBoxItems as $selectBoxItem) {
            /** @var SelectBoxItem $selectBoxItem */
            $copiedSelectBoxItem = $selectBoxItem->copy(
                $this->selectBoxItemRepository->nextIdentity(),
                $productId,
                $sectionId,
                $elementId
            );

            $this->selectBoxItemRepository->add($copiedSelectBoxItem);
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->priceType;
    }
}
