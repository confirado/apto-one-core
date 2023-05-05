<?php

namespace Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPrices;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Doctrine\Common\Collections\ArrayCollection;

class FloatInputItem extends AptoAggregate
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
     * FloatInputItem constructor.
     * @param AptoUuid $id
     * @param AptoUuid $productId
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     */
    public function __construct(AptoUuid $id, AptoUuid $productId, AptoUuid $sectionId, AptoUuid $elementId)
    {
        parent::__construct($id);
        $this->publish(
            new FloatInputItemAdded(
                $this->getId()
            )
        );
        $this->productId = $productId;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;

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
     * @return FloatInputItem
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
     * @return FloatInputItem
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
     * @return FloatInputItem
     */
    public function setElementId(AptoUuid $elementId): self
    {
        $this->elementId = $elementId;
        return $this;
    }

    /**
     * @param AptoUuid $newId
     * @param AptoUuid $newProductId
     * @param AptoUuid $newSectionId
     * @param AptoUuid $newElementId
     * @return FloatInputItem
     * @throws InvalidUuidException
     */
    public function copy(AptoUuid $newId, AptoUuid $newProductId, AptoUuid $newSectionId, AptoUuid $newElementId): FloatInputItem
    {
        // create price per unit item
        $floatInput = new FloatInputItem(
            $newId,
            $newProductId,
            $newSectionId,
            $newElementId
        );

        // set prices
        $floatInput->aptoPrices = $this->copyAptoPrices();

        // return new item
        return $floatInput;
    }
}