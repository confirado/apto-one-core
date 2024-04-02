<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Rule;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Condition\Criterion;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidTypeException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionOperator;
use Apto\Catalog\Domain\Core\Model\Product\Element\Element;
use Apto\Catalog\Domain\Core\Model\Product\Section\Section;
use Doctrine\Common\Collections\Collection;

abstract class RuleCriterion extends Criterion
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @param AptoUuid $id
     * @param Rule $rule
     * @param int|null $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param CriterionOperator $operator
     * @param string|null $value
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidValueException
     * @throws CriterionInvalidTypeException
     */
    final public function __construct(
        AptoUuid $id,
        Rule $rule,
        CriterionOperator $operator,
        ?int $type,
        ?AptoUuid $sectionId,
        ?AptoUuid $elementId,
        string $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string $value = null
    )
    {
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


        if (null !== $elementId && null !== $property && !array_key_exists($property, $rule->getProduct()->getElementSelectableValues($sectionId, $elementId))) {
            throw new CriterionInvalidPropertyException('The given property \'' . $property . '\' is not defined in the given element\'s definition.');
        }

        $this->rule = $rule;
    }

    /**
     * @param AptoUuid $id
     * @param Collection $entityMapping
     * @return $this|null
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    public function copy(AptoUuid $id, Collection &$entityMapping): ?RuleCriterion
    {
        // set rule
        $rule = $entityMapping->get($this->rule->getId()->getId());

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
}
