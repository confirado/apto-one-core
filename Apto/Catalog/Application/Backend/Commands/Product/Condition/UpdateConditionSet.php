<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class UpdateConditionSet extends ProductChildCommand
{
    /**
     * @var string
     */
    private string $conditionSetId;

    /**
     * @var string
     */
    private string $identifier;

    /**
     * @var int
     */
    private int $conditionsOperator;

    /**
     * @param string $productId
     * @param string $conditionSetId
     * @param string $identifier
     * @param int $conditionsOperator
     */
    public function __construct(string $productId, string $conditionSetId, string $identifier, int $conditionsOperator)
    {
        parent::__construct($productId);
        $this->conditionSetId = $conditionSetId;
        $this->identifier = $identifier;
        $this->conditionsOperator = $conditionsOperator;
    }

    /**
     * @return string
     */
    public function getConditionSetId(): string
    {
        return $this->conditionSetId;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return int
     */
    public function getConditionsOperator(): int
    {
        return $this->conditionsOperator;
    }
}
