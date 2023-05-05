<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceGroupInternalNameChanged extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue
     */
    private $internalName;

    /**
     * PriceGroupInternalNameChanged constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $internalName
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $internalName)
    {
        parent::__construct($id);
        $this->internalName = $internalName;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getInternalName(): AptoTranslatedValue
    {
        return $this->internalName;
    }
}