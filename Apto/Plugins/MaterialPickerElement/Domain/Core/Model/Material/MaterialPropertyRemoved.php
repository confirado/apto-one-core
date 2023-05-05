<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialPropertyRemoved extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $propertyId;

    /**
     * MaterialPropertyRemoved constructor.
     * @param AptoUuid $id
     * @param AptoUuid $propertyId
     */
    public function __construct(AptoUuid $id, AptoUuid $propertyId)
    {
        parent::__construct($id);
        $this->propertyId = $propertyId;
    }

    /**
     * @return AptoUuid
     */
    public function getPropertyId(): AptoUuid
    {
        return $this->propertyId;
    }
}