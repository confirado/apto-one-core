<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\Material;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroup;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class Pool extends AptoAggregate
{
    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * @var Collection
     */
    private $items;

    /**
     * Pool constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->items = new ArrayCollection();
        $this->name = $name;
        $this->publish(
            new PoolAdded(
                $this->getId(),
                $this->getName()
            )
        );
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @param AptoTranslatedValue $name
     * @return Pool
     */
    public function setName(AptoTranslatedValue $name): Pool
    {
        if ($this->getName()->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new PoolNameChanged(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @param Material $material
     * @param PriceGroup $priceGroup
     * @return Pool
     */
    public function addItem(Material $material, PriceGroup $priceGroup): Pool
    {
        if (!$this->assertMaterialIsUnique($material->getId())) {
            return $this;
        }

        // @todo find a solution to use material id for items index, index-by="material.id.id" don't work
        // see:
        // -https://github.com/doctrine/doctrine2/issues/1784
        // -https://github.com/doctrine/doctrine2/pull/639
        $poolItemId = $this->getNextPoolItemId();
        $this->items->set(
            $poolItemId->getId(),
            new PoolItem($poolItemId, $this, $material, $priceGroup)
        );
        $this->publish(
            new PoolItemAdded(
                $this->getId(),
                $poolItemId,
                $material->getId(),
                $priceGroup->getId()
            )
        );
        return $this;
    }

    /**
     * @param AptoUuid $itemId
     * @param PriceGroup $priceGroup
     * @return $this
     */
    public function setItemPriceGroup(AptoUuid $itemId, PriceGroup $priceGroup): Pool
    {
        /** @var PoolItem $item */
        foreach ($this->items as $item) {
            if ($item->getId()->getId() === $itemId->getId()) {
                $item->setPriceGroup($priceGroup);
                break;
            }
        }

        return $this;
    }

    /**
     * @param AptoUuid $poolItemId
     * @return Pool
     */
    public function removeItem(AptoUuid $poolItemId): Pool
    {
        if ($this->items->containsKey($poolItemId->getId())) {
            $this->items->remove($poolItemId->getId());
            $this->publish(
                new PoolItemRemoved(
                    $this->getId(),
                    $poolItemId
                )
            );
        }
        return $this;
    }

    /**
     * @param AptoUuid $materialId
     * @return AptoUuid|null
     */
    public function getItemIdByMaterialId(AptoUuid $materialId): ?AptoUuid
    {
        /** @var PoolItem $item */
        foreach ($this->items as $item) {
            if ($item->getMaterialId()->getId() === $materialId->getId()) {
                return $item->getId();
            }
        }
        return null;
    }

    /**
     * @param AptoUuid $id
     * @return Pool
     */
    public function copy(AptoUuid $id): Pool
    {
        $copiedPool = new Pool(
            $id,
            $this->getName()
        );

        /** @var PoolItem $item */
        foreach ($this->items as $item) {
            $copiedPool->addItem(
                $item->getMaterial(),
                $item->getPriceGroup()
            );
        }

        $copiedPool->publish(
            new PoolCopied(
                $id,
                $this->getId()
            )
        );

        return $copiedPool;
    }

    /**
     * @param AptoUuid $materialId
     * @return bool
     */
    private function assertMaterialIsUnique(AptoUuid $materialId): bool
    {
        /** @var PoolItem $item */
        foreach ($this->items as $item) {
            if ($item->getMaterialId()->getId() === $materialId->getId()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return AptoUuid
     */
    private function getNextPoolItemId()
    {
        return new AptoUuid();
    }
}