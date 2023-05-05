<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductCategoriesUpdated extends AbstractDomainEvent
{
    /**
     * @var array
     */
    private $categories;

    /**
     * ProductCategoriesUpdated constructor.
     * @param AptoUuid $id
     * @param array $categories
     */
    public function __construct(AptoUuid $id, array $categories)
    {
        parent::__construct($id);
        $this->categories = $categories;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
}