<?php

namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceMatrixElementPriceRemoved extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $priceId;

    /**
     * PriceMatrixPositionAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $priceId
     */
    public function __construct(AptoUuid $id, AptoUuid $priceId)
    {
        parent::__construct($id);
        $this->priceId = $priceId;
    }

    /**
     * @return AptoUuid
     */
    public function getPriceId(): AptoUuid
    {
        return $this->priceId;
    }
}