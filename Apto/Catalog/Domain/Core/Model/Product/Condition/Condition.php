<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Condition;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\Element;
use Apto\Catalog\Domain\Core\Model\Product\Section\Section;
use Doctrine\Common\Collections\Collection;

class Condition extends Criterion
{
    /**
     * @var ConditionSet
     */
    protected $conditionSet;

    /**
     * @param AptoUuid $id
     * @param ConditionSet $conditionSet
     * @param CriterionOperator $operator
     * @param int|null $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param string|null $value
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    final public function __construct(
        ConditionSet $conditionSet,
        AptoUuid $id,
        CriterionOperator $operator,
        ?int $type,
        ?AptoUuid $sectionId,
        ?AptoUuid $elementId,
        string $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string $value = null
    ) {
        parent::__construct(
            $id,
            $operator,
            $type,
            $sectionId,
            $elementId,
            $property,
            $computedProductValue,
            $value
        );

        if (null !== $elementId && null !== $property && !array_key_exists($property, $conditionSet->getProduct()->getElementSelectableValues($sectionId, $elementId))) {
            throw new CriterionInvalidPropertyException('The given property \'' . $property . '\' is not defined in the given element\'s definition.');
        }

        $this->conditionSet = $conditionSet;
    }

    /**
     * @param AptoUuid $id
     * @param Collection $entityMapping
     * @return Condition
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    public function copy(AptoUuid $id, Collection &$entityMapping): Condition
    {
        $conditionSet = $entityMapping->get($this->conditionSet->getId()->getId());

        // set section id
        $orgSectionId = $this->getSectionId();
        /** @var Section|null $section */
        $section = null === $orgSectionId ? null : $entityMapping->get($orgSectionId->getId());
        $sectionId = null === $section ? null : $section->getId();

        // set element id
        $orgElementId = $this->getElementId();
        /** @var Element|null $element */
        $element = null === $orgElementId ? null : $entityMapping->get($orgElementId->getId());
        $elementId = null === $element ? null : $element->getId();

        // set computed product value
        $computedProductValue = null === $this->getComputedProductValue() ? null : $entityMapping->get($this->getComputedProductValue()->getId()->getId());

        // return new ruleCriterion
        return new static(
            $conditionSet,
            $id,
            $this->getOperator(),
            $this->getType(),
            $sectionId,
            $elementId,
            $this->getProperty(),
            $computedProductValue,
            $this->getValue()
        );
    }
}
