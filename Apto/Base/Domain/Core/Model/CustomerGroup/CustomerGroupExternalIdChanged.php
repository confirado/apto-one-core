<?php

namespace Apto\Base\Domain\Core\Model\CustomerGroup;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class CustomerGroupExternalIdChanged extends AbstractDomainEvent
{
    /**
     * @var string|null
     */
    private $externalId;

    /**
     * CustomerGroupExternalIdChanged constructor.
     * @param AptoUuid $id
     * @param string|null $externalId
     */
    public function __construct(AptoUuid $id, string $externalId = null)
    {
        parent::__construct($id);
        $this->externalId = $externalId;
    }

    /**
     * @return string|null
     */
    public function getExternalId()
    {
        return $this->externalId;
    }
}