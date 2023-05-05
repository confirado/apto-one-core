<?php

namespace Apto\Catalog\Application\Core\Service\ShopConnector;
use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;

interface BasketConnector
{
    /**
     * @param BasketItem $basketItem
     * @param ConnectorConfig $connectorConfig
     * @param array $commands
     * @param int $quantity
     */
    public function addItem(BasketItem $basketItem, ConnectorConfig $connectorConfig, array $commands, int $quantity = 1);

    /**
     * @param array $basketItems
     * @param ConnectorConfig $connectorConfig
     * @param array $images
     * @param int $quantity
     * @param array $prices
     * @param array $commands
     * @param array $payload
     */
    public function addItems(array $basketItems, ConnectorConfig $connectorConfig, array $images, int $quantity, array $prices, array $commands, array $payload = []);

    /**
     * @param BasketItem $basketItem
     */
    public function removeItem(BasketItem $basketItem);

    /**
     * @param ConnectorConfig $connectorConfig
     * @return mixed
     */
    public function getState(ConnectorConfig $connectorConfig);
}