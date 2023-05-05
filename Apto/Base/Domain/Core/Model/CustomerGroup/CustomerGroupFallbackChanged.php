<?php

namespace Apto\Base\Domain\Core\Model\CustomerGroup;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class CustomerGroupFallbackChanged extends AbstractDomainEvent
{

    /**
     * @var bool
     */
    private $fallback;

    /**
     * CustomerGroupFallbackChanged constructor.
     * @param AptoUuid $id
     * @param bool $fallback
     */
    public function __construct(AptoUuid $id, bool $fallback)
    {
        parent::__construct($id);
        $this->fallback= $fallback;
    }

    /**
     * @return bool
     */
    public function getFallback(): bool
    {
        return $this->fallback;
    }
}