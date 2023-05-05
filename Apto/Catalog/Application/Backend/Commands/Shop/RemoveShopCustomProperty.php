<?php

namespace Apto\Catalog\Application\Backend\Commands\Shop;

use Apto\Base\Application\Core\CommandInterface;

class RemoveShopCustomProperty implements CommandInterface
{
    /**
     * @var string
     */
    private $shopId;

    /**
     * @var string
     */
    private $key;

    /**
     * @param string $shopId
     * @param string $key
     */
    public function __construct(string $shopId,  string $key)
    {
        $this->shopId = $shopId;
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getShopId(): string
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}