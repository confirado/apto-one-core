<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialClicksChanged extends AbstractDomainEvent
{
    /**
     * @var int
     */
    private $clicks;

    /**
     * MaterialClicksChanged constructor.
     * @param AptoUuid $id
     * @param int $clicks
     */
    public function __construct(AptoUuid $id, int $clicks)
    {
        parent::__construct($id);
        $this->clicks = $clicks;
    }

    /**
     * @return int
     */
    public function getClicks(): int
    {
        return $this->clicks;
    }
}