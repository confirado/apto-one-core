<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceGroupAdded extends AbstractDomainEvent
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
     * @var float
     */
    private $additionalCharge;

    /**
     * PriceGroupAdded constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     * @param AptoTranslatedValue $internalName
     * @param float $additionalCharge
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name, AptoTranslatedValue $internalName, float $additionalCharge)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->internalName = $internalName;
        $this->additionalCharge = $additionalCharge;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getInternalName(): AptoTranslatedValue
    {
        return $this->internalName;
    }

    /**
     * @return float
     */
    public function getAdditionalCharge(): float
    {
        return $this->additionalCharge;
    }
}