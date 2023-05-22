<?php

namespace Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue;

class OrderedComputedProductValues
{

    /**
     * @var array
     */
    protected $values;

    /**
     * @param array $computedValues
     * @return self
     */
    public static function fromArray(array $computedValues): self
    {
        // @todo order computed values
        $orderedComputedValues = [];
        return new self($orderedComputedValues);
    }

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

}
