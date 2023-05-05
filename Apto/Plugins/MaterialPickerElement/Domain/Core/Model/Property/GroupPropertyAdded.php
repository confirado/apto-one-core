<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class GroupPropertyAdded extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $propertyId;

    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * GroupPropertyAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $propertyId
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, AptoUuid $propertyId, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->propertyId = $propertyId;
        $this->name = $name;
    }

    /**
     * @return AptoUuid
     */
    public function getPropertyId(): AptoUuid
    {
        return $this->propertyId;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }
}