<?php

namespace Apto\Base\Domain\Core\Model\AptoPriceFormula;

use Apto\Base\Domain\Core\Model\AptoUuid;

class AptoPriceFormulaCustomerGroupIdChanged extends AbstractAptoPriceFormulaEvent
{
    /**
     * @var AptoUuid
     */
    protected $customerGroupId;

    /**
     * AptoPriceFormulaCustomerGroupIdChanged constructor.
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