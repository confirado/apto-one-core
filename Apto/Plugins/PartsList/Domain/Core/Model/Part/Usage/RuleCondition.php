<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\CompareOperator;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionOperator;
use Apto\Catalog\Domain\Core\Model\Product\Product;

class RuleCondition extends AptoEntity
{
    /**
     * @var RuleUsage
     */
    protected $rule;

    /**
     * @var AptoUuid
     */
    protected $productId;

    /**
     * @var CriterionOperator
     */
    protected $operator;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string|null
     */
    protected $sectionId;

    /**
     * @var string|null
     */
    protected $elementId;

    /**
     * @var string|null
     */
    protected $property;

    /**
     * @var string|null
     */
    protected $computedValueId;

    /**
     * @param AptoUuid $id
     * @param RuleUsage $rule
     * @param Product $product
     * @param CriterionOperator $operator
     * @param string $value
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $property
     * @param string|null $computedValueId
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidValueException
     * @throws InvalidUuidException
     */
    public function __construct(
        AptoUuid          $id,
        RuleUsage         $rule,
        Product           $product,
        CriterionOperator $operator,
        string            $value,
        string            $sectionId = null,
        string            $elementId = null,
        string            $property = null,
        string            $computedValueId = null
    ) {
        parent::__construct($id);

        if ('' == trim($property)) {
            $property = null;
        }

        if ('' == trim($value)) {
            $value = null;
        }

        if (null === $elementId && null !== $property) {
            throw new CriterionInvalidPropertyException('The given property must be null if no elementId is set.');
        }

        if (null !== $elementId && null !== $property && !array_key_exists($property, $product->getElementSelectableValues(new AptoUuid($sectionId), new AptoUuid($elementId)))) {
            throw new CriterionInvalidPropertyException('The given property \'' . $property . '\' is not defined in the given element\'s definition.');
        }

        if (CriterionOperator::NOT_ACTIVE === $operator->getOperator() || CriterionOperator::ACTIVE === $operator->getOperator()) {
            if (null !== $value) {
                throw new CriterionInvalidValueException('The given value must be empty when operator \'NOT_ACTIVE\' or \'ACTIVE\' is set.');
            }
            if (null !== $property) {
                throw new CriterionInvalidValueException('The given property must be empty when operator \'NOT_ACTIVE\' or \'ACTIVE\' is set.');
            }
            if (null !== $computedValueId) {
                throw new CriterionInvalidValueException('The given computed Value must be empty when operator \'NOT_ACTIVE\' or \'ACTIVE\' is set.');
            }
        } else {
            if ((null === $elementId || null === $property) && null === $computedValueId) {
                throw new CriterionInvalidOperatorException('The given operator must be ACTIVE or NOT_ACTIVE if no element or property is set.');
            }
        }

        $this->rule = $rule;
        $this->productId = $product->getId();
        $this->operator = $operator;
        $this->value = $value;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->property = $property;
        $this->computedValueId = $computedValueId;
    }

    /**
     * @return RuleUsage
     */
    public function getRule(): RuleUsage
    {
        return $this->rule;
    }

    /**
     * @return AptoUuid
     */
    public function getProductId(): AptoUuid
    {
        return $this->productId;
    }

    /**
     * @return CriterionOperator
     */
    public function getOperator(): CriterionOperator
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        if (null === $this->value) {
            return '';
        }
        return $this->value;
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
     * @return string
     */
    public function getComputedValueId(): ?string
    {
        return $this->computedValueId;
    }

    /**
     * @param AptoUuid $productId
     * @param State $state
     * @param array $computedValues
     * @return bool
     * @throws InvalidUuidException
     */
    public function isFulfilledBy(AptoUuid $productId, State $state, array $computedValues = []): bool
    {
        $elementId = new AptoUuid($this->elementId);
        $sectionId = new AptoUuid($this->sectionId);

        if ($productId->getId() !== $this->productId->getId()) {
            return false;
        }

        // todo consider repetition here. check isFulfilled from Defaultcriterion class
        if (null !== $this->property) {
            $value = $state->getValue($sectionId, $elementId, $this->property);
        } else if (null !== $this->elementId) {
            $value = $state->isElementActive($sectionId, $elementId) ? true : null;
        } else if (null !== $this->sectionId) {
            $value = $state->isSectionActive($sectionId) ? true : null;
        } else if (null !== $this->computedValueId) {
            if (array_key_exists($this->computedValueId, $computedValues)) {
                $value = $computedValues[$this->computedValueId];
            } else {
                return false;
            }
        } else {
            $value = ($this->productId->getId() === $productId->getId());
        }

        $compareOperator = new CompareOperator($this->operator->getOperator());
        return $compareOperator->compare($value, $this->value);
    }
}
