<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementDefaultUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $default;

    /**
     * ElementDefaultUpdated constructor.
     * @param AptoUuid $id
     * @param bool $default
     */
    public function __construct(AptoUuid $id, bool $default)
    {
        parent::__construct($id);
        $this->default = $default;
    }

    /**
     * @return bool
     */
    public function getDefault(): bool
    {
        return $this->default;
    }
}