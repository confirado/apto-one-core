<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Rule;

class RuleCriterionOperator
{
    /**
     * valid values
     */
    const NOT_ACTIVE = 0;
    const ACTIVE = 1;
    const LOWER = 2;
    const LOWER_OR_EQUAL = 3;
    const EQUAL = 4;
    const NOT_EQUAL = 5;
    const GREATER_OR_EQUAL = 6;
    const GREATER = 7;
    const CONTAINS = 8;
    const NOT_CONTAINS = 9;

    /**
     * array of all possible operators
     * @var array
     */
    protected static $validValues = array(
        self::NOT_ACTIVE,
        self::ACTIVE,
        self::LOWER,
        self::LOWER_OR_EQUAL,
        self::EQUAL,
        self::NOT_EQUAL,
        self::GREATER_OR_EQUAL,
        self::GREATER,
        self::CONTAINS,
        self::NOT_CONTAINS
    );

    /**
     * @var int
     */
    protected $operator;

    /**
     * RuleCriterionOperator constructor.
     * @param int $operator
     * @throws RuleCriterionInvalidOperatorException
     */
    public function __construct(int $operator)
    {
        if (!in_array($operator, self::$validValues)) {
            throw new RuleCriterionInvalidOperatorException('The given value \'' . $operator . '\' is not a valid operator.');
        }
        $this->operator = $operator;
    }

    /**
     * @return int
     */
    public function getOperator(): int
    {
        return $this->operator;
    }

    /**
     * Compare this operator to another one
     * @param RuleCriterionOperator $operator
     * @return bool
     */
    public function matches(RuleCriterionOperator $operator)
    {
        return $operator->getOperator() == $this->getOperator();
    }

    /**
     * @return int
     */
    public function jsonSerialize()
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->operator;
    }
}