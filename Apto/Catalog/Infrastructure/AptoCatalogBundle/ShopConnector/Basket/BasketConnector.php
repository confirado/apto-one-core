<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\ShopConnector\Basket;

use Apto\Base\Infrastructure\AptoBaseBundle\ShopConnector\CurlConnector;
use Apto\Catalog\Application\Core\Service\ShopConnector\BasketConnector as BasketConnectorInterface;
use Apto\Catalog\Application\Core\Service\ShopConnector\BasketItem;
use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;
use Apto\Base\Infrastructure\AptoBaseBundle\ShopConnector\CurlConnectorRemoteException;

class BasketConnector extends CurlConnector implements BasketConnectorInterface
{
    /**
     * @param BasketItem $basketItem
     * @param ConnectorConfig $connectorConfig
     * @param array $commands
     * @param int $quantity
     * @return array|mixed
     * @throws CurlConnectorRemoteException
     */
    public function addItem(BasketItem $basketItem, ConnectorConfig $connectorConfig, array $commands, int $quantity = 1)
    {
        $articleData = [
            'title' => $basketItem->getName(),
            'taxRate' => $basketItem->getTaxRate(),
            'prices' => $basketItem->getPrices(),
            'configId' => $basketItem->getConfigurationId(),
            'configState' => $basketItem->getBillOfMaterials(),
            'properties' => $basketItem->getProperties(),
            'additionalData' => $basketItem->getAdditionalData(),
            'commands' => $commands
        ];

        $images = $basketItem->getImages();
        if ($images) {
            $articleData['images'] = $images;
        }
        $data = [
            'query' => 'AddToBasket',
            'arguments' => [
                $articleData,
                $quantity
            ]
        ];

        return $this->request($connectorConfig, $data);
    }

    /**
     * @param array $basketItems
     * @param ConnectorConfig $connectorConfig
     * @param array $images
     * @param int $quantity
     * @param array $prices
     * @param array $commands
     * @param array $payload
     * @return array|mixed
     * @throws CurlConnectorRemoteException
     */
    public function addItems(array $basketItems, ConnectorConfig $connectorConfig, array $images, int $quantity, array $prices, array $commands, array $payload = [])
    {
        $setData = [
            'articleData' => [],
            'images' => $images,
            'payload' => $payload,
            'prices' => $prices,
            'commands' => $commands
        ];

        /** @var BasketItem $basketItem */
        foreach ($basketItems as $basketItem) {
            $setData['articleData'][] = [
                'title' => $basketItem->getName(),
                'taxRate' => $basketItem->getTaxRate(),
                'prices' => $basketItem->getPrices(),
                'configId' => $basketItem->getConfigurationId(),
                'configState' => $basketItem->getBillOfMaterials(),
                'properties' => $basketItem->getProperties(),
                'additionalData' => $basketItem->getAdditionalData()
            ];

            $articleImages = $basketItem->getImages();
            if ($articleImages) {
                $setData['articleData']['images'] = $articleImages;
            }

        }

        if ($images) {
            $setData['images'] = $images;
        }

        $data = [
            'query' => 'AddMultipleToBasket',
            'arguments' => [
                $setData,
                $quantity
            ]
        ];

        return $this->request($connectorConfig, $data);

    }
    public function removeItem(BasketItem $basketItem)
    {
        // TODO: Implement removeItem() method.
    }

    /**
     * @param ConnectorConfig $connectorConfig
     * @return array|mixed
     * @throws CurlConnectorRemoteException
     */
    public function getState(ConnectorConfig $connectorConfig)
    {
        return $this->request($connectorConfig, [
            'query' => 'GetState',
            'arguments' => []
        ]);
    }
}