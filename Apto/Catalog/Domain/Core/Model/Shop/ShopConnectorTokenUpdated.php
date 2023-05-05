<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ShopConnectorTokenUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $connectorToken;

    /**
     * ShopConnectorTokenUpdated constructor.
     * @param AptoUuid $id
     * @param string $connectorToken
     */
    public function __construct(AptoUuid $id, $connectorToken)
    {
        parent::__construct($id);
        $this->connectorToken = $connectorToken;
    }

    /**
     * @return string
     */
    public function getConnectorToken()
    {
        return $this->connectorToken;
    }
}