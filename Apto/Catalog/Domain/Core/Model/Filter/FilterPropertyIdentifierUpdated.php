<?php

namespace Apto\Catalog\Domain\Core\Model\Filter;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class FilterPropertyIdentifierUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * CategoryNameUpdated constructor.
     * @param AptoUuid $id
     * @param string $identifier
     */
    public function __construct(AptoUuid $id, string $identifier)
    {
        parent::__construct($id);
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}