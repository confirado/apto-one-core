<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ShopNameUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * ShopNameUpdated constructor.
     * @param AptoUuid $id
     * @param string $name
     */
    public function __construct(AptoUuid $id, $name)
    {
        parent::__construct($id);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}