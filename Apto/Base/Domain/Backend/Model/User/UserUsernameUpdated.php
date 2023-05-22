<?php

namespace Apto\Base\Domain\Backend\Model\User;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class UserUsernameUpdated extends AbstractDomainEvent
{
    /**
     * @var UserName
     */
    private $username;

    /**
     * UserUsernameUpdated constructor.
     * @param AptoUuid $id
     * @param UserName $username
     */
    public function __construct(AptoUuid $id, UserName $username)
    {
        parent::__construct($id);
        $this->username = $username;
    }

    /**
     * @return UserName
     */
    public function getUsername(): UserName
    {
        return $this->username;
    }
}
