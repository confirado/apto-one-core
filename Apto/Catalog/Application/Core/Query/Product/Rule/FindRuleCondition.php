<?php

namespace Apto\Catalog\Application\Core\Query\Product\Rule;

class FindRuleCondition extends AbstractFindRule
{
    /**
     * @var string
     */
    private string $conditionId;

    /**
     * @param string $ruleId
     * @param string $conditionId
     */
    public function __construct(string $ruleId, string $conditionId)
    {
        parent::__construct($ruleId);
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
