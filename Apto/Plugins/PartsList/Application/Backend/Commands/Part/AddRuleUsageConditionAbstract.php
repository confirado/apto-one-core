<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

use Apto\Base\Application\Core\CommandInterface;

abstract class AddRuleUsageConditionAbstract implements CommandInterface
{
    /**
     * @var string
     */
    private $partId;

    /**
     * @var string
     */
    private $usageId;

    /**
     * @var string
     */
    private $productId;

    /**
     * @var string|null
     */
    private $sectionId;

    /**
     * @var string|null
     */
    private $elementId;

    /**
     * @var string|null
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
     * @var string|null
     */
    private $property;

    /**
     * @param string $partId
     * @param string $usageId
     * @param string $productId
     * @param int $operator
     * @param string $value
     * @param string|null $sectionId
     * @param string|null $computedValueId
     * @param string|null $elementId
     * @param string|null $property
     */
    public function __construct(
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
        $this->partId = $partId;
        $this->usageId = $usageId;
        $this->productId = $productId;
        $this->sectionId  = $sectionId;
        $this->computedValueId  = $computedValueId;
        $this->elementId = $elementId;
        $this->value = $value;
        $this->property = $property;
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getPartId(): string
    {
        return $this->partId;
    }

    /**
     * @return string
     */
    public function getUsageId(): string
    {
        return $this->usageId;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
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
     * @return string|null
     */
    public function getProperty(): ?string
    {
        return $this->property;
    }

    /**
     * @return string|null
     */
    public function getComputedValueId(): ?string
    {
        return $this->computedValueId;
    }
}

