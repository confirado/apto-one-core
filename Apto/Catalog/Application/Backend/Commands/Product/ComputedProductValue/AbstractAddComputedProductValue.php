<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\ComputedProductValue;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

abstract class AbstractAddComputedProductValue extends ProductChildCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * AddComputedProductValue constructor.
     * @param string $productId
     * @param string $name
     */
    public function __construct(string $productId, string $name)
    {
        parent::__construct($productId);
        $this->name = strtolower($name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
