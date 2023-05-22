<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\ShopConnector;

use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;

class CurlConnector
{
    /**
     * Send a curl request by given attributes
     * @param ConnectorConfig $connectorConfig
     * @param array $data
     * @return mixed
     * @throws CurlConnectorRemoteException
     */
    protected function request(ConnectorConfig $connectorConfig, $data = [])
    {
        $curl = curl_init();
        $debug = false;

        $data['token'] = $connectorConfig->getToken();

        $parameters = [
            'encode' => 'json',
            'data' => $data
        ];

        $setOpt = [
            CURLOPT_URL => $connectorConfig->getUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HEADER => $debug,
            CURLINFO_HEADER_OUT => $debug,
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/html; charset=utf-8',
                'User-Agent: Connector API',
                'Cookie: ' . $this->sanitize($connectorConfig->getCookieHeaderString())
            ],
            CURLOPT_POSTFIELDS => json_encode($parameters)
        ];

        curl_setopt_array($curl, $setOpt);
        $response = curl_exec($curl);

        /** @phpstan-ignore-next-line  */
        if (true === $debug) {
            print_r(curl_getinfo($curl));
            print_r($response);
        }

        curl_close($curl);

        $decodedData = json_decode($response, true);

        // throw exception on remote error
        $error = null;
        if (!is_array($decodedData)) {
            $error = $response;
        } elseif (isset($decodedData['error'])) {
            $error = $decodedData['error'];
        }
        if ($error) {
            throw new CurlConnectorRemoteException($error);
        }

        return $decodedData;
    }

    /**
     * Sanitize header information
     * @param $string
     * @return mixed
     */
    private function sanitize($string)
    {
        return str_replace(
            ["\n", "\r", "\t", "\0"],
            ['',   '',   ' ',  ''],
            $string
        );
    }
}
