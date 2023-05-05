<?php

namespace Apto\Catalog\Domain\Core\Model\Category;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class CategoryParentChanged extends AbstractDomainEvent
{
    /**
     * @var Category|null
     */
    private $parent;

    /**
     * CategoryParentChanged constructor.
     * @param AptoUuid $id
     * @param Category|null $parent
     */
    public function __construct(AptoUuid $id, ?Category $parent)
    {
        parent::__construct($id);
        $this->parent = $parent;
    }

    /**
     * @return Category|null
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }
}