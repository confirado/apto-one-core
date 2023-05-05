<?php

namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceMatrixElementAdded extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $priceMatrixElementId;

    /**
     * @var PriceMatrixPosition
     */
    private $priceMatrixPosition;

    /**
     * PriceMatrixPositionAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $priceMatrixElementId
     * @param PriceMatrixPosition $priceMatrixPosition
     */
    public function __construct(AptoUuid $id, AptoUuid $priceMatrixElementId, PriceMatrixPosition $priceMatrixPosition)
    {
        parent::__construct($id);
        $this->priceMatrixElementId = $priceMatrixElementId;
        $this->priceMatrixPosition = $priceMatrixPosition;
    }

    /**
     * @return AptoUuid
     */
    public function getPriceMatrixElementId(): AptoUuid
    {
        return $this->priceMatrixElementId;
    }

    /**
     * @return PriceMatrixPosition
     */
    public function getPriceMatrixPosition(): PriceMatrixPosition
    {
        return $this->priceMatrixPosition;
    }
}