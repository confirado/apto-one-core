<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductKeepSectionOrderUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private bool $keepSectionOrder;

    /**
     * ProductKeepSectionOrderUpdated constructor.
     * @param AptoUuid $id
     * @param bool $keepSectionOrder
     */
    public function __construct(AptoUuid $id, bool $keepSectionOrder)
    {
        parent::__construct($id);
        $this->keepSectionOrder = $keepSectionOrder;
    }

    /**
     * @return bool
     */
    public function getKeepSectionOrder(): bool
    {
        return $this->keepSectionOrder;
    }
}
