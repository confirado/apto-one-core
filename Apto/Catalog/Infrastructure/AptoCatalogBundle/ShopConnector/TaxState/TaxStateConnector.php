<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\ShopConnector\TaxState;

use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;
use Apto\Base\Infrastructure\AptoBaseBundle\ShopConnector\CurlConnector;
use Apto\Base\Infrastructure\AptoBaseBundle\ShopConnector\CurlConnectorRemoteException;
use Apto\Catalog\Application\Core\Service\ShopConnector\TaxStateConnector as TaxStateConnectorInterface;

class TaxStateConnector extends CurlConnector implements TaxStateConnectorInterface
{
    /**
     * @param ConnectorConfig $connectorConfig
     * @return array|mixed
     * @throws CurlConnectorRemoteException
     */
    public function findTaxState(ConnectorConfig $connectorConfig)
    {
        $data = [
            'query' => 'GetTaxState',
            'arguments' => []
        ];

        //@todo throw exception on error
        return $this->request($connectorConfig, $data);
    }
}