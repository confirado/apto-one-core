<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Section;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class SectionIsZoomableUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $isZoomable;

    /**
     * SectionIsZoomableUpdated constructor.
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