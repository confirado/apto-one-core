<?php

namespace Apto\Base\Domain\Core\Model\CustomerGroup;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class CustomerGroupInputGrossChanged extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $inputGross;

    /**
     * CustomerGroupInputGrossChanged constructor.
     * @param AptoUuid $id
     * @param bool $inputGross
     */
    public function __construct(AptoUuid $id, bool $inputGross)
    {
        parent::__construct($id);
        $this->inputGross = $inputGross;
    }

    /**
     * @return bool
     */
    public function getInputGross(): bool
    {
        return $this->inputGross;
    }
}