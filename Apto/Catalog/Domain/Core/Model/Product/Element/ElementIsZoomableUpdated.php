<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementIsZoomableUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $isZoomable;

    // todo leave this or remove?

    /**
     * ElementIsMandatoryUpdated constructor.
     * @param AptoUuid $id
     * @param bool $isZoomable
     */
    public function __construct(AptoUuid $id, bool $isZoomable)
    {
        parent::__construct($id);
        $this->isZoomable = $isZoomable;
    }

    /**
     * @return bool
     */
    public function getIsZoomable(): bool
    {
        return $this->isZoomable;
    }
}
