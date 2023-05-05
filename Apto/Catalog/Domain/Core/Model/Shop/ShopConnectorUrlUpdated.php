<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ShopConnectorUrlUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue|null
     */
    private $connectorUrl;

    /**
     * ShopConnectorUrlUpdated constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue|null $connectorUrl
     */
    public function __construct(AptoUuid $id, $connectorUrl)
    {
        parent::__construct($id);
        $this->connectorUrl = $connectorUrl;
    }

    /**
     * @return AptoTranslatedValue|null
     */
    public function getConnectorUrl()
    {
        return $this->connectorUrl;
    }
}