<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Section;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class SectionRepeatableUpdated extends AbstractDomainEvent
{
    /**
     * @var array
     */
    private array $repeatable;

    /**
     * @param AptoUuid $id
     * @param array    $repeatable
     */
    public function __construct(AptoUuid $id, array $repeatable)
    {
        parent::__construct($id);
        $this->repeatable = $repeatable;
    }

    /**
     * @return array
     */
    public function getRepeatable(): array
    {
        return $this->repeatable;
    }
}
