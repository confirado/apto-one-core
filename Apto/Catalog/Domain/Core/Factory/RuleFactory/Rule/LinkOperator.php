<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;

use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\LinkOperatorEmptyConditionsException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\LinkOperatorInvalidValueException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class LinkOperator
{
    /**
     * valid operators
     */
    const OPERATOR_AND = 0;
    const OPERATOR_OR = 1;

    /**
     * @var array
     */
    protected static $validOperators = [
        self::OPERATOR_AND => 'AND',
        self::OPERATOR_OR => 'OR'
    ];

    /**
     * @var int
     */
    protected $operator;

    /**
     * @param int $operator
     */
    public function __construct(int $operator)
    {
        if (!array_key_exists($operator, self::$validOperators)) {
            throw new LinkOperatorInvalidValueException('The given value \'' . $operator . '\' is not a valid LinkOperator.');
        }

        $this->operator = $operator;
    }

    /**
     * Get this operator value
     * @return int
     */
    public function getOperator(): int
    {
        return $this->operator;
    }

    /**
     * Link all given criteria
     * @param State $state
     * @param RulePayload $rulePayload
     * @param Criterion[] $criteria
     * @return bool
     */
    public function areFulfilled(State $state, RulePayload $rulePayload, array $criteria): bool
    {
        if (count($criteria) == 0) {
            return true;
        }

        switch ($this->operator) {

            // all conditions must be fulfilled
            case self::OPERATOR_AND: {
                foreach ($criteria as $criterion) {
                    if ($criterion->isActive() && !$criterion->isFulfilled($state, $rulePayload)) {
                        return false;
                    }
                }
                return true;
            }

            // any condition must be fulfilled
            case self::OPERATOR_OR: {
                foreach ($criteria as $criterion) {
                    if ($criterion->isActive() && $criterion->isFulfilled($state, $rulePayload)) {
                        return true;
                    }
                }
                return false;
            }

            // something went wrong, operator should be valid at this point
            default: {
                throw new LinkOperatorInvalidValueException(sprintf(
                    'The given value \'%s\' is not a valid LinkOperator.',
                     $this->operator
                ));
            }
        }
    }

    /**
     * Change the given state to fulfill all given criteria, depending on selected operator
     * @param ConfigurableProduct $product
     * @param State $state
     * @param array $criteria
     * @param array|null $operatorsToFulfill
     * @return State
     */
    public function fulfill(ConfigurableProduct $product, State $state, array $criteria, ?array $operatorsToFulfill = null): State
    {
        if (count($criteria) == 0) {
            return $state;
        }

        switch ($this->operator) {

            // all conditions must be fulfilled
            case self::OPERATOR_AND: {
                /** @var Criterion $criterion */
                foreach ($criteria as $criterion) {
                    $state = $criterion->fulfill($product, $state, $operatorsToFulfill);
                }
                break;
            }

            // any (first?) condition must be fulfilled
            case self::OPERATOR_OR: {
                $state = $criteria[0]->fulfill($product, $state, $operatorsToFulfill);
                break;
            }

            // something went wrong, operator should be valid at this point
            default: {
                throw new LinkOperatorInvalidValueException(sprintf(
                    'The given value \'%s\' is not a valid LinkOperator.',
                    $this->operator
                ));
            }
        }

        return $state;
    }

    /**
     * Return a human-readable string representation
     * @param State $state
     * @return string
     */
    public function explain(State $state): string
    {
        return self::$validOperators[$this->operator] ?? '';
    }

}