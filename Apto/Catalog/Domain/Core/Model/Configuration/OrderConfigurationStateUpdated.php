<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class OrderConfigurationStateUpdated extends AbstractDomainEvent
{
    /**
     * @var State
     */
    private $state;

    /**
     * OrderConfigurationStateUpdated constructor.
     * @param AptoUuid $id
     * @param State $state
     */
    public function __construct(AptoUuid $id, State $state)
    {
        parent::__construct($id);
        $this->state = $state;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }
}