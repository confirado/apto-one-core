<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Condition;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\ComputedProductValueCriterion;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\DefaultCriterion;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Product;

abstract class Criterion extends AptoEntity
{
    const STANDARD_TYPE = 0;
    const COMPUTED_VALUE_TYPE = 1;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var int
     */
    protected $type;

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
     * @var CriterionOperator
     */
    protected $operator;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param AptoUuid $id
     * @param Product $product
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
     public function __construct(
         Product $product,
         AptoUuid $id,
         CriterionOperator $operator,
        ?int $type,
        ?AptoUuid $sectionId,
        ?AptoUuid $elementId,
        string $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string $value = null
    ) {
         $this->product = $product;

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
            throw new CriterionInvalidTypeException('The given type \'' . $type . '\' is invalid.');
        }

        if (null === $elementId && null !== $property && $type === DefaultCriterion::TYPE) {
            throw new CriterionInvalidPropertyException('The given property must be null if no elementId is set for a default criterion.');
        }

        if (CriterionOperator::NOT_ACTIVE === $operator->getOperator() || CriterionOperator::ACTIVE === $operator->getOperator()) {
            if (null !== $value) {
                throw new CriterionInvalidValueException('The given value must be empty when operator \'ACTIVE\' or \'NOT_ACTIVE\' is set.');
            }
            if (null !== $property) {
                throw new CriterionInvalidValueException('The given property must be empty when operator \'ACTIVE\' or \'NOT_ACTIVE\' is set.');
            }
        } else {
            if ($type === DefaultCriterion::TYPE && (null === $elementId || null === $property)) {
                throw new CriterionInvalidOperatorException('The given operator must be \'ACTIVE\' or \'NOT_ACTIVE\' if no elementId or property is set.');
            }
        }

        // if "Berechneter Wert" is selected than it has to have a value
        if ($type === ComputedProductValueCriterion::TYPE && (null === $computedProductValue || null === $value)) {
            throw new CriterionInvalidValueException('A ComputedProductValue and a value to compare with has to be set!');
        }

        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->property = $property;
        $this->operator = $operator;
        $this->value = $value;
        $this->type = $type;
        $this->computedProductValue = $computedProductValue;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
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
     * @return CriterionOperator
     */
    public function getOperator(): CriterionOperator
    {
        return $this->operator;
    }

    /**
     * @param CriterionOperator $operator
     *
     * @return $this
     */
    public function setOperator(CriterionOperator $operator): self
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
