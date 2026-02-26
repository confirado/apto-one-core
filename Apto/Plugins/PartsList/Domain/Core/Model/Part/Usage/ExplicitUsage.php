<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;

class ExplicitUsage extends Usage
{
    /**
     * @param Part $part
     * @param AptoUuid $id
     * @param Quantity $quantity
     * @param AptoUuid $productId
     */
    public function __construct(Part $part, AptoUuid $id,  Quantity $quantity, AptoUuid $productId)
    {
        parent::__construct($part, $id, $quantity, $productId);
    }
}