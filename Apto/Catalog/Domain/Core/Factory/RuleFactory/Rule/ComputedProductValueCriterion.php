<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class ComputedProductValueCriterion extends Criterion
{
    /**
     * Define type
     */
    const TYPE = 1;

    /**
     * @var string
     */
    protected $computedProductValueId;

    /**
     * @param bool $active
     * @param CompareOperator $operator
     * @param string $computedProductValueId
     * @param string|null $value
     */
    public function __construct(
        bool $active,
        CompareOperator $operator,
        ?string $value,
        string $computedProductValueId
    ) {
        // assert valid parameters
        if (in_array($operator->getOperator(), [CompareOperator::NOT_ACTIVE, CompareOperator::ACTIVE])) {
            throw new CriterionInvalidOperatorException('The given operator must not be ACTIVE or NOT_ACTIVE if criterion is of type ComputedProductValue.');
        }

        $this->computedProductValueId = $computedProductValueId;

        parent::__construct($active, $operator, $value);
    }

    /**
     * @return string|null
     */
    public function getComputedProductValueId(): string
    {
        return $this->computedProductValueId;
    }

    /**
     * @param State $state
     * @param RulePayload $rulePayload
     * @return bool
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function isFulfilled(State $state, RulePayload $rulePayload): bool
    {
        $values = $rulePayload->getComputedValues();
        if (!array_key_exists($this->computedProductValueId, $values)) {
            throw new InvalidArgumentException('The computed value with id \'' . $this->computedProductValueId . '\' does not exist.');
        }
        $value = $values[$this->computedProductValueId];

        return $this->operator->compare($value, $this->value);
    }

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @param array|null $operatorsToFulfill
     * @return State
     */
    public function fulfill(ConfigurableProduct $product, State $state, ?array $operatorsToFulfill = null): State
    {
        // cannot fulfill ComputedProductValue criterion
        return $state;
    }

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @param RulePayload $rulePayload
     * @return string
     */
    public function explain(ConfigurableProduct $product, State $state, RulePayload $rulePayload): string
    {
        $name = 'ComputedProductValueId ' . $this->computedProductValueId;
        return $name . ' ' . parent::explain($product, $state, $rulePayload);
    }

}