<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ShopDescriptionUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $description;

    /**
     * ShopDescriptionUpdated constructor.
     * @param AptoUuid $id
     * @param string $description
     */
    public function __construct(AptoUuid $id, $description)
    {
        parent::__construct($id);
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}