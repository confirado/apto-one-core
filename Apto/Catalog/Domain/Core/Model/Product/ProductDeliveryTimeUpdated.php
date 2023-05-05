<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductDeliveryTimeUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $deliveryTime;

    /**
     * ProductDeliveryTimeUpdated constructor.
     * @param AptoUuid $id
     * @param string $deliveryTime
     */
    public function __construct(AptoUuid $id, string $deliveryTime)
    {
        parent::__construct($id);
        $this->deliveryTime = $deliveryTime;
    }

    /**
     * @return string
     */
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }
}