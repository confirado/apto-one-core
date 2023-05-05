<?php

namespace Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPrices;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Doctrine\Common\Collections\ArrayCollection;

use Apto\Base\Domain\Core\Model\InvalidUuidException;

class PricePerUnitItem extends AptoAggregate
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
     * PricePerUnitItem constructor.
     * @param AptoUuid $id
     * @param AptoUuid $productId
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     */
    public function __construct(AptoUuid $id, AptoUuid $productId, AptoUuid $sectionId, AptoUuid $elementId)
    {
        parent::__construct($id);
        $this->publish(
            new PricePerUnitItemAdded(
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
     * @return PricePerUnitItem
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
     * @return PricePerUnitItem
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
     * @return PricePerUnitItem
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
     * @return PricePerUnitItem
     * @throws InvalidUuidException
     */
    public function copy(AptoUuid $newId, AptoUuid $newProductId, AptoUuid $newSectionId, AptoUuid $newElementId): PricePerUnitItem
    {
        // create price per unit item
        $pricePerUnit = new PricePerUnitItem(
            $newId,
            $newProductId,
            $newSectionId,
            $newElementId
        );

        // set prices
        $pricePerUnit->aptoPrices = $this->copyAptoPrices();

        // return new item
        return $pricePerUnit;
    }
}