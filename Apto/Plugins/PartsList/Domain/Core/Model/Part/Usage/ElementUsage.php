<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;

class ElementUsage extends Usage
{
    /**
     * @var Value
     */
    protected $value;

    /**
     * @var QuantityCalculation
     */
    private $quantityCalculation;

    /**
     * @var AptoUuid
     */
    protected $usageForUuid;

    /**
     * ElementUsage constructor.
     * @param Part $part
     * @param AptoUuid $id
     * @param AptoUuid $usageForUuid
     * @param Quantity $quantity
     * @param Value $value
     * @param AptoUuid $productId
     */
    public function __construct(Part $part, AptoUuid $id, AptoUuid $usageForUuid, Quantity $quantity, Value $value, AptoUuid $productId)
    {
        parent::__construct($part, $id, $quantity, $productId);
        $this->value = $value;
        $this->usageForUuid = $usageForUuid;
        $this->quantityCalculation = new QuantityCalculation(
            false,
            null,
            null,
            null,
            null
        );
    }

    /**
     * @return Value
     */
    public function getValue(): Value
    {
        return $this->value;
    }

    /**
     * @param Value $value
     * @return $this
     */
    public function setValue(Value $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return QuantityCalculation
     */
    public function getQuantityCalculation(): QuantityCalculation
    {
        return $this->quantityCalculation;
    }

    /**
     * @param QuantityCalculation $quantityCalculation
     * @return ElementUsage
     */
    public function setQuantityCalculation(QuantityCalculation $quantityCalculation): ElementUsage
    {
        $this->quantityCalculation = $quantityCalculation;
        return $this;
    }

    /**
     * @return AptoUuid
     */
    public function getUsageForUuid(): AptoUuid
    {
        return $this->usageForUuid;
    }

    /**
     * @param AptoUuid $usageForUuid
     * @return $this
     */
    public function setUsageForUuid(AptoUuid $usageForUuid): self
    {
        $this->usageForUuid = $usageForUuid;
        return $this;
    }
}
