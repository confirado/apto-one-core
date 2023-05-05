<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductShopsUpdated extends AbstractDomainEvent
{
    /**
     * @var array
     */
    private $shops;

    /**
     * ProductShopsUpdated constructor.
     * @param AptoUuid $id
     * @param array $shops
     */
    public function __construct(AptoUuid $id, array $shops)
    {
        parent::__construct($id);
        $this->shops = $shops;
    }

    /**
     * @return array
     */
    public function getShops(): array
    {
        return $this->shops;
    }
}