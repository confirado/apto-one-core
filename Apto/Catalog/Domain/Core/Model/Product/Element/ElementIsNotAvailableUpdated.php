<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementIsNotAvailableUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $isNotAvailable;

    /**
     * ElementIsNotAvailableUpdated constructor.
     * @param AptoUuid $id
     * @param bool $isNotAvailable
     */
    public function __construct(AptoUuid $id, bool $isNotAvailable)
    {
        parent::__construct($id);
        $this->isNotAvailable = $isNotAvailable;
    }

    /**
     * @return bool
     */
    public function getIsNotAvailable(): bool
    {
        return $this->isNotAvailable;
    }
}