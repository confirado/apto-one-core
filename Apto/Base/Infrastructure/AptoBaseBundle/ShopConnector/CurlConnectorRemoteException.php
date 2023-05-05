<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\ShopConnector;

class CurlConnectorRemoteException extends \Exception
{

    /**
     * CurlConnectorRemoteException constructor.
     * @param string $remoteErrorMessage
     */
    public function __construct($remoteErrorMessage)
    {
        parent::__construct('CurlConnector request failed, host returned: ' . $remoteErrorMessage);
    }

}