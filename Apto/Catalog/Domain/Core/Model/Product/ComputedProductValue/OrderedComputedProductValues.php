<?php

namespace Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue;

class OrderedComputedProductValues
{

    /**
     * @var ComputedProductValue
     */
    protected $values;

    /**
     * @param array $computedValues
     * @return static
     */
    public static function fromArray(array $computedValues): self
    {
        // @todo order computed values
        $orderedComputedValues = [];
        return new self($orderedComputedValues);
    }

    /**
     * @param ComputedProductValue[] $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return ComputedProductValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

}