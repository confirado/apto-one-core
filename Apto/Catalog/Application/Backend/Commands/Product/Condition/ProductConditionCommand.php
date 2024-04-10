<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

abstract class ProductConditionCommand extends ProductChildCommand
{
    /**
     * @var string
     */
    private $conditionId;

    /**
     * @param string $productId
     * @param string $conditionId
     */
    public function __construct(string $productId, string $conditionId)
    {
        parent::__construct($productId);
        $this->conditionId = $conditionId;
    }

    /**
     * @return string
     */
    public function getConditionId(): string
    {
        return $this->conditionId;
    }
}
