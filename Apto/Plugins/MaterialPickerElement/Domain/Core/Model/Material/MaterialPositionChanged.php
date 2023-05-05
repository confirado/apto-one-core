<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialPositionChanged extends AbstractDomainEvent
{
    /**
     * @var int
     */
    private $position;

    /**
     * MaterialClicksChanged constructor.
     * @param AptoUuid $id
     * @param int $position
     */
    public function __construct(AptoUuid $id, int $position)
    {
        parent::__construct($id);
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
