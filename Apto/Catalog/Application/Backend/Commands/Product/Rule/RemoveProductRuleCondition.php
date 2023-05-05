<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

class RemoveProductRuleCondition extends ProductRuleCommand
{
    /**
     * @var string
     */
    private $conditionId;

    /**
     * RemoveProductRuleCondition constructor.
     * @param string $productId
     * @param string $ruleId
     * @param string $conditionId
     */
    public function __construct(string $productId, string $ruleId, string $conditionId)
    {
        parent::__construct($productId, $ruleId);
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