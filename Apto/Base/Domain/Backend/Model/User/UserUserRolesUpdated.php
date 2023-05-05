<?php

namespace Apto\Base\Domain\Backend\Model\User;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class UserUserRolesUpdated extends AbstractDomainEvent
{
    /**
     * @var array
     */
    public $userRoles;

    /**
     * UserUserRolesUpdated constructor.
     * @param AptoUuid $id
     * @param array $userRoles
     */
    public function __construct(AptoUuid $id, array $userRoles)
    {
        parent::__construct($id);
        $this->userRoles = $userRoles;
    }

    /**
     * @return array
     */
    public function getUserRoles(): array
    {
        return $this->userRoles;
    }
}