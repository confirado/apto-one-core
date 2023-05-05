<?php

namespace Apto\Base\Domain\Core\Model\CustomerGroup;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class CustomerGroupShopIdChanged extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $shopId;

    /**
     * CustomerGroupShopIdChanged constructor.
     * @param AptoUuid $id
     * @param AptoUuid $shopId
     */
    public function __construct(AptoUuid $id, AptoUuid $shopId)
    {
        parent::__construct($id);
        $this->shopId = $shopId;
    }

    /**
     * @return AptoUuid
     */
    public function getShopId(): AptoUuid
    {
        return $this->shopId;
    }
}