<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceGroupAdditionalChargeChanged extends AbstractDomainEvent
{
    /**
     * @var float
     */
    private $additionalCharge;

    /**
     * PriceGroupAdditionalChargeChanged constructor.
     * @param AptoUuid $id
     * @param float $additionalCharge
     */
    public function __construct(AptoUuid $id, float $additionalCharge)
    {
        parent::__construct($id);
        $this->additionalCharge = $additionalCharge;
    }

    /**
     * @return float
     */
    public function getAdditionalCharge(): float
    {
        return $this->additionalCharge;
    }
}