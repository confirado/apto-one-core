<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload;

use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;

class RulePayload
{
    /**
     * @var ComputedProductValue[]
     */
    protected $computedValues;

    /**
     * @param ComputedProductValue[] $computedValues
     */
    public function __construct(array $computedValues)
    {
        $this->computedValues = $computedValues;
    }

    /**
     * @return ComputedProductValue[]
     */
    public function getComputedValues(): array
    {
        return $this->computedValues;
    }
}