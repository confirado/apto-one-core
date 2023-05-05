<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\ComputedProductValue;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveAlias extends ProductChildCommand
{
    /**
     * @var string
     */
    private $computedProductValueId;

    /**
     * @var string
     */
    private $id;

    /**
     * AddAlias constructor.
     * @param string $computedProductValueId
     * @param string $id
     */
    public function __construct(
        string $productId,
        string $computedProductValueId,
        string $id
    ) {
        parent::__construct($productId);
        $this->computedProductValueId = $computedProductValueId;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getComputedProductValueId(): string
    {
        return $this->computedProductValueId;
    }
}
