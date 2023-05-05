<?php

namespace Apto\Catalog\Domain\Core\Model\Group;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class GroupIdentifierUpdated extends AbstractDomainEvent
{
    /**
     * @var string|null
     */
    private $identifier;

    /**
     * GroupIdentifierUpdated constructor.
     * @param AptoUuid $id
     * @param string|null $identifier
     */
    public function __construct(AptoUuid $id, string $identifier = null)
    {
        parent::__construct($id);
        $this->identifier = $identifier;
    }

    /**
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}