<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandInterface;

class AddPriceMatrixElementCustomProperty implements CommandInterface
{
    /**
     * @var string
     */
    private $priceMatrixId;

    /**
     * @var string
     */
    private $priceMatrixElementId;

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
     * AddPriceMatrixElementCustomProperty constructor.
     * @param string $priceMatrixId
     * @param string $priceMatrixElementId
     * @param string $key
     * @param array|string $value
     * @param bool $translatable
     */
    public function __construct(string $priceMatrixId, string $priceMatrixElementId, string $key, $value, bool $translatable = false)
    {
        $this->priceMatrixId = $priceMatrixId;
        $this->priceMatrixElementId = $priceMatrixElementId;
        $this->key = $key;
        $this->value = $translatable ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
        $this->translatable = $translatable;
    }

    /**
     * @return string
     */
    public function getPriceMatrixId(): string
    {
        return $this->priceMatrixId;
    }

    /**
     * @return string
     */
    public function getPriceMatrixElementId(): string
    {
        return $this->priceMatrixElementId;
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
