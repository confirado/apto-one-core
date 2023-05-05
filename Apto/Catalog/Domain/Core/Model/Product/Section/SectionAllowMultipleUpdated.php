<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Section;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class SectionAllowMultipleUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $allowMultiple;

    /**
     * SectionAllowMultipleUpdated constructor.
     * @param AptoUuid $id
     * @param bool $allowMultiple
     */
    public function __construct(AptoUuid $id, bool $allowMultiple)
    {
        parent::__construct($id);
        $this->allowMultiple = $allowMultiple;
    }

    /**
     * @return bool
     */
    public function getAllowMultiple(): bool
    {
        return $this->allowMultiple;
    }
}