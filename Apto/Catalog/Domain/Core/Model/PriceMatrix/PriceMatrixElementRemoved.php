<?php

namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceMatrixElementRemoved extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $priceMatrixElementId;

    /**
     * PriceMatrixPositionAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $priceMatrixElementId
     */
    public function __construct(AptoUuid $id, AptoUuid $priceMatrixElementId)
    {
        parent::__construct($id);
        $this->priceMatrixElementId = $priceMatrixElementId;
    }

    /**
     * @return AptoUuid
     */
    public function getPriceMatrixElementId(): AptoUuid
    {
        return $this->priceMatrixElementId;
    }
}