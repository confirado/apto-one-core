<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialIsNotAvailableChanged extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $isNotAvailable;

    /**
     * MaterialIsNotAvailableChanged constructor.
     * @param AptoUuid $id
     * @param bool $isNotAvailable
     */
    public function __construct(AptoUuid $id, bool $isNotAvailable)
    {
        parent::__construct($id);
        $this->isNotAvailable = $isNotAvailable;
    }

    /**
     * @return bool
     */
    public function getIsNotAvailable(): bool
    {
        return $this->isNotAvailable;
    }
}