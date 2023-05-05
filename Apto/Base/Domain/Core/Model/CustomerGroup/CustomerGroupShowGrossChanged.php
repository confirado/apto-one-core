<?php

namespace Apto\Base\Domain\Core\Model\CustomerGroup;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class CustomerGroupShowGrossChanged extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $showGross;

    /**
     * CustomerGroupShowGrossChanged constructor.
     * @param AptoUuid $id
     * @param bool $showGross
     */
    public function __construct(AptoUuid $id, bool $showGross)
    {
        parent::__construct($id);
        $this->showGross = $showGross;
    }

    /**
     * @return bool
     */
    public function getShowGross(): bool
    {
        return $this->showGross;
    }
}