<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidTypeException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Symfony\Component\Form\Exception\InvalidArgumentException;

abstract class Criterion
{
    /**
     * Define type
     */
    const TYPE = null;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var CompareOperator
     */
    protected $operator;

    /**
     * @var string|null
     */
    protected $value;

    /**
     * @param bool $active
     * @param CompareOperator $operator
     * @param string|null $value
     */
    public function __construct(
        bool $active,
        CompareOperator $operator,
        string $value = null
    ) {
        // sanitize input values
        if ('' === trim($value)) {
            $value = null;
        }

        $this->active = $active;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return CompareOperator
     */
    public function getOperator(): CompareOperator
    {
        return $this->operator;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Check, if criterion is fulfilled by given state
     * @param State $state
     * @param RulePayload $rulePayload
     * @return bool
     */
    abstract function isFulfilled(State $state, RulePayload $rulePayload): bool;

    /**
     * Change the given state to fulfill
     * @param ConfigurableProduct $product
     * @param State $state
     * @param array|null $operatorsToFulfill
     * @return State
     */
    abstract public function fulfill(ConfigurableProduct $product, State $state, ?array $operatorsToFulfill = null): State;

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @param RulePayload $rulePayload
     * @return string
     */
    public function explain(ConfigurableProduct $product, State $state, RulePayload $rulePayload): string
    {
        $flags = [];
        if (!$this->active)
            $flags[] = 'inactive';
        $flags[] = $this->isFulfilled($state, $rulePayload) ? 'fulfilled' : 'failed';

        return sprintf(
            '%s %s(%s)',
            $this->operator->explain($state),
            $this->value !== null ? '"' . $this->value . '" ' : '',
            implode(', ', $flags)
        );
    }

}