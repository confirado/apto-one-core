<?php

namespace Apto\Base\Domain\Core\Model\AptoDiscount;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;

use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;

class AptoDiscount extends AptoEntity
{
    /**
     * @var float
     */
    protected $discount;

    /**
     * @var AptoUuid
     */
    protected $customerGroupId;

    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var AptoTranslatedValue
     */
    protected $description;

    /**
     * AptoDiscount constructor.
     * @param AptoUuid $id
     * @param float $discount
     * @param AptoUuid $customerGroupId
     * @param AptoTranslatedValue $name
     * @throws InvalidTranslatedValueException
     */
    public function __construct(AptoUuid $id, float $discount, AptoUuid $customerGroupId, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->discount = $discount;
        $this->customerGroupId = $customerGroupId;
        $this->name = $name;
        $this->description = new AptoTranslatedValue([]);
    }

    /**
     * @param float $discount
     * @return AptoDiscount
     */
    public function setDiscount(float $discount): AptoDiscount
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @param AptoUuid $customerGroupId
     * @return AptoDiscount
     */
    public function setCustomerGroupId(AptoUuid $customerGroupId): AptoDiscount
    {
        $this->customerGroupId = $customerGroupId;
        return $this;
    }

    /**
     * @return AptoUuid
     */
    public function getCustomerGroupId(): AptoUuid
    {
        return $this->customerGroupId;
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
     * @return AptoDiscount
     */
    public function setName(AptoTranslatedValue $name): AptoDiscount
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getDescription(): AptoTranslatedValue
    {
        return $this->description;
    }

    /**
     * @param AptoTranslatedValue $description
     * @return AptoDiscount
     */
    public function setDescription(AptoTranslatedValue $description): AptoDiscount
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @return AptoDiscount
     * @throws InvalidTranslatedValueException
     */
    public function copy(AptoUuid $id): AptoDiscount
    {
        //create new discount
        $discount = new AptoDiscount(
            $id,
            $this->getDiscount(),
            $this->getCustomerGroupId(),
            $this->getName()
        );

        // set description
        $discount->setDescription($this->getDescription());

        // return copy
        return $discount;
    }
}