<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

class UpdateRuleUsageCondition extends AddRuleUsageConditionAbstract
{
    /**
     * @var string
     */
    private $conditionId;

    /**
     * UpdateRuleUsageCondition constructor.
     * @param string $conditionId
     * @param string $partId
     * @param string $usageId
     * @param string $productId
     * @param int $operator
     * @param string $value
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $property
     */
    public function __construct(
        string $conditionId,
        string $partId,
        string $usageId,
        string $productId,
        int $operator,
        string $value,
        string $sectionId = null,
        string $elementId = null,
        string $property = null,
        string $computedValueId = null
    ) {
        parent::__construct($partId, $usageId, $productId, $operator, $value , $sectionId, $elementId, $property, $computedValueId);
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

