<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator;

use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\DefaultElementPriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\PriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ProductPriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ProductSurchargeProvider;


class PriceCalculatorRegistry
{
    /**
     * @var array
     */
    private $priceCalculators;

    /**
     * @var array
     */
    private $priceProviders;

    /**
     * @var array
     */
    private $productPriceProviders;

    /**
     * @var array
     */
    private $productSurchargeProviders;

    /**
     * PriceCalculatorRegistry constructor.
     */
    public function __construct()
    {
        $this->priceCalculators = [];
        $this->priceProviders = [];
        $this->productPriceProviders = [];
        $this->productSurchargeProviders = [];
    }

    /**
     * @param PriceCalculator $priceCalculator
     */
    public function addPriceCalculator(PriceCalculator $priceCalculator)
    {
        $id = $priceCalculator->getId();
        if (array_key_exists($id, $this->priceCalculators)) {
            throw new \InvalidArgumentException('A price calculator with an id \'' . $id . '\' is already registered.');
        }

        $this->priceCalculators[$id] = $priceCalculator;
    }

    /**
     * @param string $id
     * @return PriceCalculator
     */
    public function getPriceCalculatorById(string $id): PriceCalculator
    {
        if (!array_key_exists($id, $this->priceCalculators)) {
            throw new \InvalidArgumentException('A price calculator with an id \'' . $id . '\' is not registered.');
        }

        return $this->priceCalculators[$id];
    }

    /**
     * @return array
     */
    public function getPriceCalculatorList(): array
    {
        $priceCalculatorList = [];

        foreach ($this->priceCalculators as $priceCalculator) {
            /** @var PriceCalculator $priceCalculator */
            $priceCalculatorList[] = [
                'id' => $priceCalculator->getId(),
                'name' => $priceCalculator->getName()
            ];
        }

        return $priceCalculatorList;
    }

    /**
     * @param PriceProvider $elementPriceProvider
     */
    public function addPriceProvider(PriceProvider $elementPriceProvider)
    {
        $elementDefinition = $elementPriceProvider->getElementDefinitionClass();
        if (array_key_exists($elementDefinition, $this->priceProviders)) {
            throw new \InvalidArgumentException('A element price provider with an id \'' . $elementDefinition . '\' is already registered.');
        }

        $this->priceProviders[$elementDefinition] = $elementPriceProvider;
    }

    /**
     * @param string $elementDefinition
     * @return PriceProvider|null
     */
    public function getPriceProvider(string $elementDefinition): PriceProvider
    {
        if (!array_key_exists($elementDefinition, $this->priceProviders)) {
            return new DefaultElementPriceProvider();
        }

        return $this->priceProviders[$elementDefinition];
    }

    /**
     * @param ProductPriceProvider $productPriceProvider
     */
    public function addProductPriceProvider(ProductPriceProvider $productPriceProvider)
    {
        $id = trim($productPriceProvider->getId());

        if ($id === '') {
            throw new \InvalidArgumentException('ProductPriceProvider->getId() is not allowed to return an empty string.');
        }

        $this->productPriceProviders[$id] = $productPriceProvider;
    }

    /**
     * @return array
     */
    public function getProductPriceProviders()
    {
        return $this->productPriceProviders;
    }

    /**
     * @param ProductSurchargeProvider $productSurchargeProvider
     */
    public function addProductSurchargeProvider(ProductSurchargeProvider $productSurchargeProvider)
    {
        $id = trim($productSurchargeProvider->getId());

        if ($id === '') {
            throw new \InvalidArgumentException('ProductSurchargeProvider->getId() is not allowed to return an empty string.');
        }

        $this->productSurchargeProviders[$id] = $productSurchargeProvider;
    }

    /**
     * @return array
     */
    public function getProductSurchargeProviders()
    {
        return $this->productSurchargeProviders;
    }
}