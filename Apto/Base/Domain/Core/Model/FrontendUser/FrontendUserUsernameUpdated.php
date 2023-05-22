<?php

namespace Apto\Base\Domain\Core\Model\FrontendUser;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class FrontendUserUsernameUpdated extends AbstractDomainEvent
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
