<?php

namespace Apto\Catalog\Domain\Core\Model\Filter;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class FilterPropertyFilterCategoriesUpdated extends AbstractDomainEvent
{
    /**
     * @var array
     */
    private $filterCategories;

    /**
     * CategoryNameUpdated constructor.
     * @param AptoUuid $id
     * @param array $filterCategories
     */
    public function __construct(AptoUuid $id, array $filterCategories)
    {
        parent::__construct($id);
        $this->filterCategories = $filterCategories;
    }

    /**
     * @return array
     */
    public function getFilterCategories()
    {
        return $this->filterCategories;
    }
}