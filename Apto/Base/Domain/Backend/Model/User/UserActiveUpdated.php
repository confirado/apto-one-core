<?php

namespace Apto\Base\Domain\Backend\Model\User;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class UserActiveUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $active;

    /**
     * UserActiveUpdated constructor.
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