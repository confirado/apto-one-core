<?php

namespace Apto\Base\Domain\Core\Model\FrontendUser;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class FrontendUserExternalCustomerGroupIdUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $externalCustomerGroupId;

    /**
     * FrontendUserExternalCustomerGroupIdUpdated constructor.
     * @param AptoUuid $id
     * @param string $externalCustomerGroupId
     */
    public function __construct(AptoUuid $id, string $externalCustomerGroupId)
    {
        parent::__construct($id);
        $this->externalCustomerGroupId = $externalCustomerGroupId;
    }

    /**
     * @return string
     */
    public function getExternalCustomerGroupId(): string
    {
        return $this->externalCustomerGroupId;
    }
}
