<?php

namespace Apto\Base\Domain\Core\Model\FrontendUser;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;
use Apto\Base\Domain\Core\Model\Email;

class FrontendUserEmailUpdated extends AbstractDomainEvent
{
    /**
     * @var Email
     */
    private $email;

    /**
     * UserEmailUpdated constructor.
     * @param AptoUuid $id
     * @param Email $email
     */
    public function __construct(AptoUuid $id, Email $email)
    {
        parent::__construct($id);
        $this->email = $email;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }
}
