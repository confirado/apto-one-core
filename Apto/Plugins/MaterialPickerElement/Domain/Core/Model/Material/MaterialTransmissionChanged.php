<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialTransmissionChanged extends AbstractDomainEvent
{
    /**
     * @var int|null
     */
    private $transmission;

    /**
     * MaterialTransmissionChanged constructor.
     * @param AptoUuid $id
     * @param int|null $transmission
     */
    public function __construct(AptoUuid $id, $transmission)
    {
        parent::__construct($id);
        $this->transmission = $transmission;
    }

    /**
     * @return int|null
     */
    public function getTransmission()
    {
        return $this->transmission;
    }
}