<?php

namespace Apto\Base\Domain\Core\Model\FrontendUser;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class FrontendUserCustomerNumberUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private string $customerNumber;

    /**
     * @param AptoUuid $id
     * @param string $customerNumber
     */
    public function __construct(AptoUuid $id, string $customerNumber)
    {
        parent::__construct($id);
        $this->customerNumber = $customerNumber;
    }

    /**
     * @return string
     */
    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }
}
