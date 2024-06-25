<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveConditionSet extends ProductChildCommand
{
    /**
     * @var string
     */
    private string $conditionSetId;

    /**
     * @param string $productId
     * @param string $conditionSetId
     */
    public function __construct(string $productId, string $conditionSetId)
    {
        parent::__construct($productId);
        $this->conditionSetId = $conditionSetId;
    }

    /**
     * @return string
     */
    public function getConditionSetId(): string
    {
        return $this->conditionSetId;
    }
}
