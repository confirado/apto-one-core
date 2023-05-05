<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\ComputedProductValue;

class UpdateComputedProductValue extends AbstractAddComputedProductValue
{
    /**
     * @var string
     */
    private $computedValueId;

    /**
     * @var string
     */
    private $formula;

    /**
     * UpdateComputedProductValue constructor.
     * @param string $productId
     * @param string $name
     * @param string $computedValueId
     * @param string $formula
     */
    public function __construct(string $productId, string $computedValueId, string $name, string $formula)
    {
        parent::__construct($productId, $name);
        $this->computedValueId = $computedValueId;
        $this->formula = $formula;
    }

    /**
     * @return string
     */
    public function getFormula(): string
    {
        return $this->formula;
    }

    /**
     * @return string
     */
    public function getComputedValueId(): string
    {
        return $this->computedValueId;
    }
}
