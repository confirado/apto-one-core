<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

abstract class AddProductCriterion extends ProductChildCommand
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
     * @param int|null $type
     * @param int $operator
     * @param string $value
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $property
     * @param string|null $computedValueId
     */
    public function __construct(string $productId, ?int $type, int $operator, string $value, string $sectionId = null, string $elementId = null, string $property = null, string $computedValueId = null)
    {
        parent::__construct($productId);
        $this->type = $type === null ? 0 : $type;
        $this->operator = $operator;
        $this->value = $value;
        $this->sectionId = '' !== $sectionId ? $sectionId : null;
        $this->elementId = '' !== $elementId ? $elementId : null;
        $this->property = '' !== $property ? $property : null;
        $this->computedValueId = '' !== $computedValueId ? $computedValueId : null;
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
