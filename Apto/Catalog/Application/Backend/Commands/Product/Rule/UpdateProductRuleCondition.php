<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

class UpdateProductRuleCondition extends AddProductRuleCriterion
{
    /**
     * @var string
     */
    private string $conditionId;

    /**
     * @param string      $productId
     * @param string      $ruleId
     * @param string      $conditionId
     * @param int         $type
     * @param int         $operator
     * @param string      $value
     * @param string|null $computedValueId
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $property
     * @param string|null $group
     * @param string|null $groupProperty
     */
    public function __construct(
        string $productId,
        string $ruleId,
        string $conditionId,
        int $type,
        int $operator,
        string $value,
        string $computedValueId = null,
        string $sectionId = null,
        string $elementId = null,
        string $property = null,
        string $group = null,
        string $groupProperty = null,
    ) {
        parent::__construct($productId, $ruleId, $type, $sectionId, $elementId, $property, $computedValueId, $operator, $value, $group, $groupProperty);

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
