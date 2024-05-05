<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

class UpdateConditionSetCondition extends AddConditionSetCriterion
{
    /**
     * @var string
     */
    private string $conditionId;

    /**
     * @param string $productId
     * @param string $conditionSetId
     * @param string $conditionId
     * @param int $type
     * @param int $operator
     * @param string $value
     * @param string|null $computedValueId
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $property
     */
    public function __construct(
        string $productId,
        string $conditionSetId,
        string $conditionId,
        int $type,
        int $operator,
        string $value,
        string $computedValueId = null,
        string $sectionId = null,
        string $elementId = null,
        string $property = null,
    ) {
        parent::__construct($productId, $conditionSetId, $type, $sectionId, $elementId, $property, $computedValueId, $operator, $value);

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
