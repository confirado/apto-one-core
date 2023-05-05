<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductHiddenUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $hidden;

    /**
     * ProductHiddenUpdated constructor.
     * @param AptoUuid $id
     * @param bool $hidden
     */
    public function __construct(AptoUuid $id, bool $hidden)
    {
        parent::__construct($id);
        $this->hidden = $hidden;
    }

    /**
     * @return bool
     */
    public function getHidden(): bool
    {
        return $this->hidden;
    }
}