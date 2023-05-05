<?php

namespace Apto\Base\Domain\Backend\Model\UserLicence;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class UserLicenceValidSinceUpdated extends AbstractDomainEvent
{
    /**
     * @var \DateTimeImmutable
     */
    private $validSince;

    /**
     * UserLicenceValidSinceUpdated constructor.
     * @param AptoUuid $id
     * @param \DateTimeImmutable $validSince
     */
    public function __construct(AptoUuid $id, \DateTimeImmutable $validSince)
    {
        parent::__construct($id);
        $this->validSince = $validSince;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getValidSince(): \DateTimeImmutable
    {
        return $this->validSince;
    }
}