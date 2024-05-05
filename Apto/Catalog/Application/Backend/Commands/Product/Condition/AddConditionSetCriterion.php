<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

abstract class AddConditionSetCriterion extends ConditionSetCommand
{
    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string|null
     */
    private $elementId;

    /**
     * @var string|null
     */
    private $property;

    /**
     * @var string
     */
    private $computedValueId;

    /**
     * @var int
     */
    private $operator;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $productId
     * @param string $conditionSetId
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $property
     * @param int $operator
     * @param string $value
     * @param int|null $type
     * @param string|null $computedValueId
     */
    public function __construct(string $productId, string $conditionSetId, ?int $type, string $sectionId = null, string $elementId = null, string $property = null, string $computedValueId = null, int $operator, string $value)
    {
        parent::__construct($productId, $conditionSetId);
        $this->type = $type === null ? 0 : $type;
        $this->sectionId = '' !== $sectionId ? $sectionId : null;
        $this->elementId = '' !== $elementId ? $elementId : null;
        $this->property = '' !== $property ? $property : null;
        $this->computedValueId = '' !== $computedValueId ? $computedValueId : null;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string|null
     */
    public function getSectionId(): ?string
    {
        return $this->sectionId;
    }

    /**
     * @return string|null
     */
    public function getElementId(): ?string
    {
        return $this->elementId;
    }

    /**
     * @return string|null
     */
    public function getProperty(): ?string
    {
        return $this->property;
    }

    /**
     * @return int
     */
    public function getOperator(): int
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getComputedValueId(): ?string
    {
        return $this->computedValueId;
    }
}
