<?php

namespace Apto\Base\Domain\Core\Model\AptoPrice;

use Apto\Base\Domain\Core\Model\AptoUuid;

class AptoPriceCustomerGroupIdChanged extends AbstractAptoPriceEvent
{
    /**
     * @var AptoUuid
     */
    protected $customerGroupId;

    /**
     * AptoPriceCustomerGroupIdChanged constructor.
     * @param AptoUuid $id
     * @param AptoUuid $reference
     * @param AptoUuid $customerGroupId
     */
    public function __construct(AptoUuid $id, AptoUuid $reference, AptoUuid $customerGroupId)
    {
        parent::__construct($id, $reference);
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return AptoUuid
     */
    public function getCustomerGroupId(): AptoUuid
    {
        return $this->customerGroupId;
    }
}