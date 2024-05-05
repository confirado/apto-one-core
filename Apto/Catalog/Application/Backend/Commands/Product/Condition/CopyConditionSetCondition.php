<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

class CopyConditionSetCondition extends ConditionSetCommand
{

    /**
     * @var string
     */
    private string $conditionId;

    /**
     * @param string $productId
     * @param string $conditionSetId
     * @param string $conditionId
     */
    public function __construct(string $productId, string $conditionSetId, string $conditionId)
    {
        parent::__construct($productId, $conditionSetId);
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
