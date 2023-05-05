<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Section;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class SectionIsHiddenUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $isHidden;

    /**
     * SectionIsHiddenUpdated constructor.
     * @param AptoUuid $id
     * @param bool $isHidden
     */
    public function __construct(AptoUuid $id, bool $isHidden)
    {
        parent::__construct($id);
        $this->isHidden = $isHidden;
    }

    /**
     * @return bool
     */
    public function getIsHidden(): bool
    {
        return $this->isHidden;
    }
}