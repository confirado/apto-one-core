<?php

namespace Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPrices;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Doctrine\Common\Collections\ArrayCollection;

class SelectBoxItem extends AptoAggregate
{
    use AptoPrices;

    /**
     * @var AptoUuid
     */
    protected $productId;

    /**
     * @var AptoUuid
     */
    protected $sectionId;

    /**
     * @var AptoUuid
     */
    protected $elementId;

    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isDefault;

    /**
     * SelectBoxItem constructor.
     * @param AptoUuid $id
     * @param AptoUuid $productId
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, AptoUuid $productId, AptoUuid $sectionId, AptoUuid $elementId, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->publish(
            new SelectBoxItemAdded(
                $this->getId()
            )
        );
        $this->productId = $productId;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->name = $name;
        $this->isDefault = false;

        $this->aptoPrices = new ArrayCollection();
    }

    /**
     * @return AptoUuid
     */
    public function getProductId(): AptoUuid
    {
        return $this->productId;
    }

    /**
     * @param AptoUuid $productId
     * @return SelectBoxItem
     */
    public function setProductId(AptoUuid $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return AptoUuid
     */
    public function getSectionId(): AptoUuid
    {
        return $this->sectionId;
    }

    /**
     * @param AptoUuid $sectionId
     * @return SelectBoxItem
     */
    public function setSectionId(AptoUuid $sectionId): self
    {
        $this->sectionId = $sectionId;
        return $this;
    }


    /**
     * @return AptoUuid
     */
    public function getElementId(): AptoUuid
    {
        return $this->elementId;
    }

    /**
     * @param AptoUuid $elementId
     * @return SelectBoxItem
     */
    public function setElementId(AptoUuid $elementId): self
    {
        $this->elementId = $elementId;
        return $this;
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
     * @return SelectBoxItem
     */
    public function setName(AptoTranslatedValue $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     * @return SelectBoxItem
     */
    public function setIsDefault(bool $isDefault): SelectBoxItem
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    /**
     * @param AptoUuid $newId
     * @param AptoUuid $newProductId
     * @param AptoUuid $newSectionId
     * @param AptoUuid $newElementId
     * @return SelectBoxItem
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function copy(AptoUuid $newId, AptoUuid $newProductId, AptoUuid $newSectionId, AptoUuid $newElementId): SelectBoxItem
    {
        // create selectbox item
        $selectBoxItem = new SelectBoxItem(
            $newId,
            $newProductId,
            $newSectionId,
            $newElementId,
            $this->getName()
        );

        // set prices
        $selectBoxItem->aptoPrices = $this->copyAptoPrices();

        // set is default
        $selectBoxItem->setIsDefault($this->getIsDefault());

        // return new item
        return $selectBoxItem;
    }
}
