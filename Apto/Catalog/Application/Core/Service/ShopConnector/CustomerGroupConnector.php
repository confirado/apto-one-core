<?php

namespace Apto\Catalog\Application\Core\Service\ShopConnector;

use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;

interface CustomerGroupConnector
{
    /**
     * @param ConnectorConfig $connectorConfig
     * @return mixed
     */
    public function findAll(ConnectorConfig $connectorConfig);
}