<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;

class ElementUsage extends Usage
{
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
     * @param AptoUuid $producId
     */
    public function __construct(Part $part, AptoUuid $id, AptoUuid $usageForUuid, Quantity $quantity, AptoUuid $producId)
    {
        parent::__construct($part, $id, $quantity, $producId);
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