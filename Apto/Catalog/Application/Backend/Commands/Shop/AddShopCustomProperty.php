<?php

namespace Apto\Catalog\Application\Backend\Commands\Shop;

use Apto\Base\Application\Core\CommandInterface;

class AddShopCustomProperty implements CommandInterface
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
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $translatable;

    /**
     * @param string $shopId
     * @param string $key
     * @param string|array $value
     * @param bool $translatable
     */
    public function __construct(string $shopId, string $key, $value, bool $translatable = false)
    {
        $this->shopId = $shopId;
        $this->key = $key;
        $this->value = $translatable ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
        $this->translatable = $translatable;
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

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function getTranslatable(): bool
    {
        return $this->translatable;
    }
}
