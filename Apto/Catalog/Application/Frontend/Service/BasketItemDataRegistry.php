<?php

namespace Apto\Catalog\Application\Frontend\Service;

class BasketItemDataRegistry
{
    /**
     * @var array
     */
    private $basketItemDataProviders;

    /**
     * BasketItemDataRegistry constructor.
     */
    public function __construct()
    {
        $this->basketItemDataProviders = [];
    }

    /**
     * @param BasketItemDataProvider $basketItemDataProvider
     */
    public function addBasketItemDataProvider(BasketItemDataProvider $basketItemDataProvider)
    {
        $className = get_class($basketItemDataProvider);

        if (array_key_exists($className, $this->basketItemDataProviders)) {
            throw new \InvalidArgumentException('A BasketItemDataProvider with an id \'' . $className . '\' is already registered.');
        }

        $this->basketItemDataProviders[$className] = $basketItemDataProvider;
    }

    /**
     * @return array
     */
    public function getBasketItemDataProviders(): array
    {
        return $this->basketItemDataProviders;
    }
}