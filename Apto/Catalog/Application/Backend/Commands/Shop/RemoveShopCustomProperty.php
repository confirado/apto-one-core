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
    private $id;

    /**
     * @param string $shopId
     * @param string $id
     */
    public function __construct(string $shopId,  string $id)
    {
        $this->shopId = $shopId;
        $this->id = $id;
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
    public function getId(): string
    {
        return $this->id;
    }
}
