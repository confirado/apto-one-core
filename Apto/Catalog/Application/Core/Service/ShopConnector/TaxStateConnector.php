<?php

namespace Apto\Catalog\Application\Core\Service\ShopConnector;

use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;

interface TaxStateConnector
{
    /**
     * @param ConnectorConfig $connectorConfig
     * @return mixed
     */
    public function findTaxState(ConnectorConfig $connectorConfig);
}