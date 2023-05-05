<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

class UpdateProductRule extends ProductRuleCommand
{
    /**
     * @var string
     */
    private $ruleName;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var array
     */
    private $errorMessage;

    /**
     * @var int
     */
    private $conditionsOperator;

    /**
     * @var int
     */
    private $implicationsOperator;

    /**
     * @var bool
     */
    private $softRule;

    /**
     * UpdateProductRule constructor.
     * @param string $productId
     * @param string $ruleId
     * @param string $ruleName
     * @param bool $active
     * @param array $errorMessage
     * @param int $conditionsOperator
     * @param int $implicationsOperator
     * @param bool $softRule
     */
    public function __construct(string $productId, string $ruleId, string $ruleName, bool $active = true, array $errorMessage = [], int $conditionsOperator = 0, int $implicationsOperator = 0, $softRule = false)
    {
        parent::__construct($productId, $ruleId);
        $this->active = $active;
        $this->ruleName = $ruleName;
        $this->errorMessage = $errorMessage;
        $this->conditionsOperator = $conditionsOperator;
        $this->implicationsOperator = $implicationsOperator;
        $this->softRule = $softRule;
    }

    /**
     * @return string
     */
    public function getRuleName(): string
    {
        return $this->ruleName;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return array
     */
    public function getErrorMessage(): array
    {
        return $this->errorMessage;
    }

    /**
     * @return int
     */
    public function getConditionsOperator(): int
    {
        return $this->conditionsOperator;
    }

    /**
     * @return int
     */
    public function getImplicationsOperator(): int
    {
        return $this->implicationsOperator;
    }

    /**
     * @return bool
     */
    public function getSoftRule(): bool
    {
        return $this->softRule;
    }
}