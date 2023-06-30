<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;

class SectionUsage extends Usage
{
    /**
     * @var AptoUuid
     */
    protected $usageForUuid;

    /**
     * SectionUsage constructor.
     * @param Part $part
     * @param AptoUuid $id
     * @param AptoUuid $usageForUuid
     * @param Quantity $quantity
     * @param AptoUuid $productId
     */
    public function __construct(Part $part, AptoUuid $id, AptoUuid $usageForUuid, Quantity $quantity, AptoUuid $productId)
    {
        parent::__construct($part, $id, $quantity, $productId);
        $this->usageForUuid = $usageForUuid;
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