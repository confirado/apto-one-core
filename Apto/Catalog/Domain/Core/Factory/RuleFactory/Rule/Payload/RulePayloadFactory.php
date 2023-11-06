<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload;

use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class RulePayloadFactory
{
    /**
     * @var ComputedProductValueCalculator
     */
    protected $computedProductValueCalculator;

    /**
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     */
    public function __construct(ComputedProductValueCalculator $computedProductValueCalculator)
    {
        $this->computedProductValueCalculator = $computedProductValueCalculator;
    }

    /**
     * @param ConfigurableProduct $product
     * @param State               $state
     * @param bool                $indexedById
     *
     * @return RulePayload
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException
     */
    public function getPayload(ConfigurableProduct $product, State $state, bool $indexedById = true)
    {
        return new RulePayload(
            $this->computedProductValueCalculator->calculateComputedValuesByProduct($product->getProduct(), $state, $indexedById)

            // todo maybe use this?
            // $this->computedProductValueCalculator->calculateComputedValues($product->getId(), $state, $indexedById)
        );
    }

}
