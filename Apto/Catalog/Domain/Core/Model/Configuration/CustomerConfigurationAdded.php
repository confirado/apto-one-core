<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class CustomerConfigurationAdded extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $productId;

    /**
     * @var AptoUuid
     */
    private $customerId;

    /**
     * @var State
     */
    private $state;

    /**
     * ConfigurationStateUpdated constructor.
     * @param AptoUuid $id
     * @param AptoUuid $productId
     * @param AptoUuid $customerId
     * @param State $state
     */
    public function __construct(AptoUuid $id, AptoUuid $productId, AptoUuid $customerId, State $state)
    {
        parent::__construct($id);
        $this->productId = $productId;
        $this->customerId = $customerId;
        $this->state = $state;
    }

    /**
     * @return AptoUuid
     */
    public function getProductId(): AptoUuid
    {
        return $this->productId;
    }

    /**
     * @return AptoUuid
     */
    public function getCustomerId(): AptoUuid
    {
        return $this->customerId;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }
}