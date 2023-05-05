<?php

namespace Apto\Base\Domain\Backend\Model\User;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class UserRteUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $rte;

    /**
     * UserRteUpdated constructor.
     * @param AptoUuid $id
     * @param string $rte
     */
    public function __construct(AptoUuid $id, string $rte)
    {
        parent::__construct($id);
        $this->rte = $rte;
    }

    /**
     * @return string
     */
    public function getRte(): string
    {
        return $this->rte;
    }
}