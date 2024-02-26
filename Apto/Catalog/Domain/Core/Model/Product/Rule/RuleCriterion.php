<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Rule;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\ComputedProductValueCriterion;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\DefaultCriterion;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\Element;
use Apto\Catalog\Domain\Core\Model\Product\Section\Section;
use Doctrine\Common\Collections\Collection;

abstract class RuleCriterion extends AptoEntity
{
    const STANDARD_TYPE = 0;
    const COMPUTED_VALUE_TYPE = 1;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var AptoUuid|null
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
     * @var ComputedProductValue
     */
    protected $computedProductValue;

    /**
     * @var RuleCriterionOperator
     */
    protected $operator;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param AptoUuid $id
     * @param Rule $rule
     * @param int|null $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param RuleCriterionOperator $operator
     * @param string|null $value
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidValueException
     * @throws RuleCriterionInvalidTypeException
     */
    final public function __construct(
        AptoUuid $id,
        Rule $rule,
        RuleCriterionOperator $operator,
        ?int $type,
        ?AptoUuid $sectionId,
        ?AptoUuid $elementId,
        string $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string $value = null
    ) {
        parent::__construct($id);

        if (null === $type) {
            $type = 0;
        }

        if ('' == trim($property)) {
            $property = null;
        }

        if ('' == trim($value)) {
            $value = null;
        }

        if (!in_array($type, [DefaultCriterion::TYPE, ComputedProductValueCriterion::TYPE])) {
            throw new RuleCriterionInvalidTypeException('The given type \'' . $type . '\' is invalid.');
        }

        if (null === $elementId && null !== $property && $type === DefaultCriterion::TYPE) {
            throw new RuleCriterionInvalidPropertyException('The given property must be null if no elementId is set for a default criterion.');
        }

        if (null !== $elementId && null !== $property && !array_key_exists($property, $rule->getProduct()->getElementSelectableValues($sectionId, $elementId))) {
            throw new RuleCriterionInvalidPropertyException('The given property \'' . $property . '\' is not defined in the given element\'s definition.');
        }

        if (RuleCriterionOperator::NOT_ACTIVE === $operator->getOperator() || RuleCriterionOperator::ACTIVE === $operator->getOperator()) {
            if (null !== $value) {
                throw new RuleCriterionInvalidValueException('The given value must be empty when operator \'ACTIVE\' or \'NOT_ACTIVE\' is set.');
            }
            if (null !== $property) {
                throw new RuleCriterionInvalidValueException('The given property must be empty when operator \'ACTIVE\' or \'NOT_ACTIVE\' is set.');
            }
        } else {
            if ($type === DefaultCriterion::TYPE && (null === $elementId || null === $property)) {
                throw new RuleCriterionInvalidOperatorException('The given operator must be \'ACTIVE\' or \'NOT_ACTIVE\' if no elementId or property is set.');
            }
        }

        // if "Berechneter Wert" is selected than it has to have a value
        if ($type === ComputedProductValueCriterion::TYPE && (null === $computedProductValue || null === $value)) {
            throw new RuleCriterionInvalidValueException('A ComputedProductValue and a value to compare with has to be set!');
        }

        $this->rule = $rule;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->property = $property;
        $this->operator = $operator;
        $this->value = $value;
        $this->type = $type;
        $this->computedProductValue = $computedProductValue;
    }

    /**
     * @return AptoUuid|null
     */
    public function getSectionId(): ?AptoUuid
    {
        return $this->sectionId;
    }

    /**
     * @param AptoUuid|null $sectionId
     *
     * @return $this
     */
    public function setSectionId(?AptoUuid $sectionId): self
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    /**
     * @return AptoUuid|null
     */
    public function getElementId(): ?AptoUuid
    {
        return $this->elementId;
    }

    /**
     * @param AptoUuid|null $elementId
     *
     * @return $this
     */
    public function setElementId(?AptoUuid $elementId): self
    {
        $this->elementId = $elementId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProperty(): ?string
    {
        return $this->property;
    }

    /**
     * @param string|null $property
     *
     * @return $this
     */
    public function setProperty(?string $property): self
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return RuleCriterionOperator
     */
    public function getOperator(): RuleCriterionOperator
    {
        return $this->operator;
    }

    /**
     * @param RuleCriterionOperator $operator
     *
     * @return $this
     */
    public function setOperator(RuleCriterionOperator $operator): self
    {
        $this->operator = $operator;

        return $this;
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
     * @param string $value
     * @return self
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @param Collection $entityMapping
     * @return RuleCriterion|null
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidTypeException
     * @throws RuleCriterionInvalidValueException
     */
    public function copy(AptoUuid $id, Collection &$entityMapping): ?RuleCriterion
    {
        // set rule
        $rule = $entityMapping->get($this->rule->getId()->getId());

        // set section id
        $orgSectionId = $this->getSectionId();
        $sectionId = null;

        if ($orgSectionId !== null) {
            /** @var Section|null $section */
            $section = $entityMapping->get($orgSectionId->getId());

            // $section case is when we copy product, $orgSectionId case when we copy rule
            $sectionId = $section === null ? $orgSectionId : $section->getId();
        }

        // set element id
        $orgElementId = $this->getElementId();
        $elementId = null;

        if ($orgElementId !== null) {
            /** @var Element|null $element */
            $element = $entityMapping->get($orgElementId->getId());

            // $element = null case is when we copy product, $orgSectionId case when we copy rule
            $elementId = $element === null ? $orgElementId : $element->getId();
        }

        /*
         * if $entityMapping contains computedProductValue then we copy product, otherwise it should not be set
         * and we copy rule or criteria or implication.
         * see Product -> copy method
         */
        $orgComputedProductValue = $this->getComputedProductValue();
        $computedProductValue = null;

        // we copy product case
        if ($orgComputedProductValue !== null) {
            if ($entityMapping->get($this->getComputedProductValue()->getId()->getId()) !== null) {
                $computedProductValue = $entityMapping->get($this->getComputedProductValue()->getId()->getId());
            } else {
                $computedProductValue = $orgComputedProductValue;
            }
        }

        // return new ruleCriterion
        return new static(
            $id,
            $rule,
            $this->getOperator(),
            $this->getType(),
            $sectionId,
            $elementId,
            $this->getProperty(),
            $computedProductValue,
            $this->getValue()
        );
    }

    /**
     * @return Rule
     */
    public function getRule(): Rule
    {
        return $this->rule;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return ComputedProductValue
     */
    public function getComputedProductValue(): ?ComputedProductValue
    {
        return $this->computedProductValue;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @param ComputedProductValue|null $computedProductValue
     * @return void
     */
    public function setComputedProductValue(?ComputedProductValue $computedProductValue = null): void
    {
        $this->computedProductValue = $computedProductValue;
    }
}
