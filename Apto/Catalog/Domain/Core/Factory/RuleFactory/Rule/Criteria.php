<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;

use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\InvalidCriteriaElementsException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

abstract class Criteria
{
    /**
     * @var LinkOperator
     */
    protected $operator;

    /**
     * @var Criterion[]
     */
    protected $criteria;

    /**
     * @param LinkOperator $operator
     * @param Criterion[] $criteria
     */
    public function __construct(LinkOperator $operator, array $criteria)
    {
        foreach ($criteria as $criterion) {
            if (!$criterion instanceof Criterion) {
                throw new InvalidCriteriaElementsException('All elements in given criteria must be of type Criterion.');
            }
        }

        $this->operator = $operator;
        $this->criteria = $criteria;
    }

    /**
     * @return LinkOperator
     */
    public function getOperator(): LinkOperator
    {
        return $this->operator;
    }

    /**
     * @return Criterion[]
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * Check, if criteria are fulfilled by given state
     * @param State $state
     * @param RulePayload $rulePayload
     * @return bool
     */
    public function isFulfilled(State $state, RulePayload $rulePayload): bool
    {
        return $this->operator->areFulfilled($state, $rulePayload, $this->criteria);
    }

    /**
     * Change the given state to fulfill these criteria
     * @param ConfigurableProduct $product
     * @param State $state
     * @param array|null $operatorsToFulfill
     * @return State
     */
    public function fulfill(ConfigurableProduct $product, State $state, ?array $operatorsToFulfill = null): State
    {
        return $this->operator->fulfill($product, $state, $this->criteria, $operatorsToFulfill);
    }

    /**
     * Return a human-readable string representation
     * @param ConfigurableProduct $product
     * @param State $state
     * @param RulePayload $rulePayload
     * @return string
     */
    public function explain(ConfigurableProduct $product, State $state, RulePayload $rulePayload): string
    {
        $criteria = array_map(
            function(Criterion $criterion) use ($product, $state, $rulePayload) {
                return $criterion->explain($product, $state, $rulePayload);
            },
            $this->criteria
        );

        return implode(
            "\n" . $this->operator->explain($state) . ' ',
            $criteria
        );
    }
}