<?php

namespace Apto\Catalog\Application\Core\Service\Substitute;

use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;

class SubstituteResolverService
{
    /**
     * @var ComputedProductValueCalculator
     */
    private $computedProductValueCalculator;

    /**
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     */
    public function __construct(ComputedProductValueCalculator $computedProductValueCalculator)
    {
        $this->computedProductValueCalculator = $computedProductValueCalculator;
    }

    /**
     * Resolves ${name}-placeholders in the given text using the product's calculated Computed Values.
     *
     * @param string $text
     * @param State $state
     * @param string $productId
     * @return string
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException
     */
    public function resolveComputedValueSubstitutes(string $text, State $state, string $productId): string
    {
        $computedValues = $this->computedProductValueCalculator->calculateComputedValues($productId, $state);

        return preg_replace_callback('/\$\{([^}]+)\}/', function (array $match) use ($computedValues) {
            return array_key_exists($match[1], $computedValues) ? $computedValues[$match[1]] : $match[0];
        }, $text);
    }
}
