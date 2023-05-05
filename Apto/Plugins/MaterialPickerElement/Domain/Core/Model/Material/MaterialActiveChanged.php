<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialActiveChanged extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $active;

    /**
     * MaterialActiveChanged constructor.
     * @param AptoUuid $id
     * @param bool $active
     */
    public function __construct(AptoUuid $id, bool $active)
    {
        parent::__construct($id);
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }
}