<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;

class PriceGroup extends AptoAggregate
{
    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * @var AptoTranslatedValue
     */
    private $internalName;

    /**
     * @var float;
     */
    private $additionalCharge;

    /**
     * @var PriceMatrix
     */
    private $priceMatrix;

    /**
     * PriceGroup constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     * @param AptoTranslatedValue $internalName
     * @param float $additionalCharge
     * @param PriceMatrix|null $priceMatrix
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name, AptoTranslatedValue $internalName, float $additionalCharge, ?PriceMatrix $priceMatrix = null)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->internalName = $internalName;
        $this->additionalCharge = $additionalCharge;

        if (null !== $priceMatrix) {
            $this->priceMatrix = $priceMatrix;
        } else {
            $this->priceMatrix = new PriceMatrix();
        }

        $this->publish(
            new PriceGroupAdded(
                $this->getId(),
                $this->getName(),
                $this->getInternalName(),
                $this->getAdditionalCharge()
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
     * @return PriceGroup
     */
    public function setName(AptoTranslatedValue $name): PriceGroup
    {
        if ($this->getName()->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new PriceGroupNameChanged(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getInternalName(): AptoTranslatedValue
    {
        return $this->internalName;
    }

    /**
     * @param AptoTranslatedValue $internalName
     * @return PriceGroup
     */
    public function setInternalName(AptoTranslatedValue $internalName): PriceGroup
    {
        if ($this->getInternalName()->equals($internalName)) {
            return $this;
        }
        $this->internalName = $internalName;
        $this->publish(
            new PriceGroupInternalNameChanged(
                $this->getId(),
                $this->getInternalName()
            )
        );
        return $this;
    }

    /**
     * @return float
     */
    public function getAdditionalCharge(): float
    {
        return $this->additionalCharge;
    }

    /**
     * @param float $additionalCharge
     * @return PriceGroup
     */
    public function setAdditionalCharge(float $additionalCharge): PriceGroup
    {
        if ($this->getAdditionalCharge() === $additionalCharge) {
            return $this;
        }
        $this->additionalCharge = $additionalCharge;
        $this->publish(
            new PriceGroupAdditionalChargeChanged(
                $this->getId(),
                $this->getAdditionalCharge()
            )
        );
        return $this;
    }

    /**
     * @return PriceMatrix
     */
    public function getPriceMatrix(): PriceMatrix
    {
        return $this->priceMatrix;
    }

    /**
     * @param PriceMatrix $priceMatrix
     * @return PriceGroup
     */
    public function setPriceMatrix(PriceMatrix $priceMatrix): PriceGroup
    {
        if ($this->priceMatrix->equals($priceMatrix)) {
            return $this;
        }

        $this->priceMatrix = $priceMatrix;
        return $this;
    }
}