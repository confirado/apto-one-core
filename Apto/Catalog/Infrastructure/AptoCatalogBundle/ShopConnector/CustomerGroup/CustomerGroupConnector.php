<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\ShopConnector\CustomerGroup;

use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;
use Apto\Base\Infrastructure\AptoBaseBundle\ShopConnector\CurlConnector;
use Apto\Catalog\Application\Core\Service\ShopConnector\CustomerGroupConnector as CustomerGroupConnectorInterface;

class CustomerGroupConnector extends CurlConnector implements CustomerGroupConnectorInterface
{
    /**
     * @param ConnectorConfig $connectorConfig
     * @return mixed
     */
    public function findAll(ConnectorConfig $connectorConfig)
    {
        $data = [
            'query' => 'GetCustomerGroups',
            'arguments' => []
        ];

        //@todo throw exception on error
        return $this->request($connectorConfig, $data);
    }
}