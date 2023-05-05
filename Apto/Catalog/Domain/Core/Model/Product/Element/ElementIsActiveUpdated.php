<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementIsActiveUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $isActive;

    /**
     * ElementIsActiveUpdated constructor.
     * @param AptoUuid $id
     * @param bool $isActive
     */
    public function __construct(AptoUuid $id, bool $isActive)
    {
        parent::__construct($id);
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }
}