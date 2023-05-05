<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;

abstract class AbstractPriceProvider
{
    const PRICE_TYPE = 'SET_PRICE_TYPE_IN_CHILD_CLASS';

    /**
     * @var string
     */
    protected $priceType;

    /**
     * AbstractPriceExportProvider constructor.
     * @throws PriceTypeNotSetException
     */
    public function __construct()
    {
        $this->priceType = static::PRICE_TYPE;

        if ($this->priceType === self::PRICE_TYPE) {
            throw new PriceTypeNotSetException(static::class);
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->priceType;
    }
}