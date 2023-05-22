<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementPercentageSurchargeUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $sectionId;

    /**
     * @var AptoUuid
     */
    private $elementId;

    /**
     * @var float
     */
    private $percentageSurcharge;

    /**
     * @param AptoUuid $id
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param float $percentageSurcharge
     */
    public function __construct(AptoUuid $id, AptoUuid $sectionId, AptoUuid $elementId, float $percentageSurcharge)
    {
        parent::__construct($id);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->percentageSurcharge = $percentageSurcharge;
    }

    /**
     * @return AptoUuid
     */
    public function getSectionId(): AptoUuid
    {
        return $this->sectionId;
    }

    /**
     * @return AptoUuid
     */
    public function getElementId(): AptoUuid
    {
        return $this->elementId;
    }

    /**
     * @return float
     */
    public function getPercentageSurcharge(): float
    {
        return $this->percentageSurcharge;
    }
}
