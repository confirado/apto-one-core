<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\ComputedProductValue;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveComputedProductValue extends ProductChildCommand
{
    /**
     * @var string
     */
    private $id;

    /**
     * RemoveComputedProductValue constructor.
     * @param string $productId
     * @param string $id
     */
    public function __construct(string $productId, string $id)
    {
        parent::__construct($productId);
        $this->id = $id;

    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
