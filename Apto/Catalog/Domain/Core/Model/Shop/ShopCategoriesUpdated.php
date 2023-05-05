<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ShopCategoriesUpdated extends AbstractDomainEvent
{
    /**
     * @var array
     */
    private $categories;

    /**
     * ShopCategoriesUpdated constructor.
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