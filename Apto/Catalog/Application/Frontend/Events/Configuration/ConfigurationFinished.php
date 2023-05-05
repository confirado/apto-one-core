<?php
namespace Apto\Catalog\Application\Frontend\Events\Configuration;

use Apto\Base\Application\Core\EventInterface;
use Apto\Catalog\Application\Core\Service\ShopConnector\BasketItem;

class ConfigurationFinished implements EventInterface
{
    /**
     * @var BasketItem
     */
    private $basketItem;

    /**
     * @var int
     */
    private $quantity;

    /**
     * ConfigurationFinished constructor.
     * @param BasketItem $basketItem
     * @param int $quantity
     */
    public function __construct(BasketItem $basketItem, int $quantity)
    {
        $this->basketItem = $basketItem;
        $this->quantity = $quantity;
    }

    /**
     * @return BasketItem
     */
    public function getBasketItem(): BasketItem
    {
        return $this->basketItem;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}