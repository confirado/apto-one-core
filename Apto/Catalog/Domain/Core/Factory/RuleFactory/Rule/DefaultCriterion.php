<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class DefaultCriterion extends Criterion
{
    /**
     * Define type
     */
    const TYPE = 0;

    /**
     * @var AptoUuid
     */
    protected $sectionId;

    /**
     * @var AptoUuid|null
     */
    protected $elementId;

    /**
     * @var string|null
     */
    protected $property;


    /**
     * @param bool $active
     * @param CompareOperator $operator
     * @param string|null $value
     * @param AptoUuid $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     */
    public function __construct(
        bool $active,
        CompareOperator $operator,
        ?string $value,
        AptoUuid $sectionId,
        ?AptoUuid $elementId,
        ?string $property
    ) {
        // sanitize input values
        if ('' === trim($property)) {
            $property = null;
        }
        if ('' === trim($value)) {
            $value = null;
        }

        // assert valid parameters
        if (null === $elementId && null !== $property) {
            throw new CriterionInvalidPropertyException('The given property must be null if no elementId is set.');
        }
        if (in_array($operator->getOperator(), [CompareOperator::NOT_ACTIVE, CompareOperator::ACTIVE])) {
            if (null !== $property) {
                throw new CriterionInvalidPropertyException('The given property must be empty when operator ACTIVE or NOT_ACTIVE is set.');
            }
            if (null !== $value) {
                throw new CriterionInvalidValueException('The given value must be empty when operator ACTIVE or NOT_ACTIVE is set.');
            }
        } else {
            if (null === $elementId || null === $property) {
                throw new CriterionInvalidOperatorException('The given operator must be ACTIVE or NOT_ACTIVE if no elementId or property is set.');
            }
        }

        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->property = $property;

        parent::__construct($active, $operator, $value);
    }

    /**
     * @return AptoUuid
     */
    public function getSectionId(): AptoUuid
    {
        return $this->sectionId;
    }

    /**
     * @return AptoUuid|null
     */
    public function getElementId(): ?AptoUuid
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
     * @param State $state
     * @param RulePayload $rulePayload
     * @return bool
     */
    public function isFulfilled(State $state, RulePayload $rulePayload): bool
    {
        if (null !== $this->property) {
            $value = $state->getValue($this->sectionId, $this->elementId, $this->property);
        } else if (null !== $this->elementId) {
            $value = $state->isElementActive($this->sectionId, $this->elementId) ? true : null;
        } else {
            $value = $state->isSectionActive($this->sectionId) ? true : null;
        }

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
        // skip unwanted operators
        if (null !== $operatorsToFulfill && !in_array($this->operator->getOperator(), $operatorsToFulfill)) {
            return $state;
        }

        return $this->operator->fulfill($product, $state, $this->sectionId, $this->elementId, $this->property, $this->value);
    }

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @param RulePayload $rulePayload
     * @return string
     */
    public function explain(ConfigurableProduct $product, State $state, RulePayload $rulePayload): string
    {
        $name = $product->getSection($this->sectionId)['identifier'] ?? $this->sectionId;
        if ($this->elementId) {
            $name .= '.' . ($product->getElement($this->sectionId, $this->elementId)['identifier'] ?? $this->elementId);
        }
        if ($this->property) {
            $name .= '.' . $this->property;
        }
        return $name . ' ' . parent::explain($product, $state, $rulePayload);
    }

}