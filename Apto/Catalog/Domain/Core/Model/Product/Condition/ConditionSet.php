<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Condition;

use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Product;

class ConditionSet extends AptoEntity
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
        self::OPERATOR_AND,
        self::OPERATOR_OR
    ];

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Identifier
     */
    protected $identifier;

    /**
     * @var int
     */
    protected $conditionsOperator;

    /**
     * @var Collection
     */
    private $conditions;

    /**
     * @param AptoUuid $id
     * @param Product $product
     * @param Identifier $identifier
     */
    public function __construct(AptoUuid $id, Product $product, Identifier $identifier)
    {
        parent::__construct($id);
        $this->product = $product;
        $this->identifier = $identifier;
        $this->conditionsOperator = self::OPERATOR_AND;
        $this->conditions = new ArrayCollection();
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return Identifier
     */
    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @param Identifier $identifier
     * @return $this
     */
    public function setIdentifier(Identifier $identifier): ConditionSet
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return int
     */
    public function getConditionsOperator(): int
    {
        return $this->conditionsOperator;
    }

    /**
     * @param int $operator
     * @return $this
     * @throws CriterionInvalidOperatorException
     */
    public function setConditionsOperator(int $operator): ConditionSet
    {
        if (!in_array($operator, self::$validOperators)) {
            throw new CriterionInvalidOperatorException('The operator \'' . $operator . '\' is invalid.');
        }
        $this->conditionsOperator = $operator;
        return $this;
    }

    /**
     * @param CriterionOperator $operator
     * @param int $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param string|null $value
     * @return $this
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    public function addCondition(
        CriterionOperator $operator,
        int $type = 0,
        ?AptoUuid $sectionId = null,
        ?AptoUuid $elementId = null,
        ?string $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string $value = null
    ): ConditionSet {
        $conditionId = $this->nextConditionId();
        $this->conditions->set(
            $conditionId->getId(),
            new Condition(
                $this,
                $conditionId,
                $operator,
                $type,
                $sectionId,
                $elementId,
                $property,
                $computedProductValue,
                $value,
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @return $this
     */
    public function removeCondition(AptoUuid $conditionId): ConditionSet
    {
        if ($this->hasCondition($conditionId)) {
            $this->conditions->remove($conditionId->getId());
        }
        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @return Condition|null
     */
    public function getCondition(AptoUuid $conditionId): ?Condition
    {
        if ($this->hasCondition($conditionId)) {
            return $this->conditions->get($conditionId->getId());
        }

        return null;
    }

    /**
     * @param AptoUuid $conditionId
     * @param Collection $entityMapping
     * @return AptoUuid|null
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    public function copyCondition(AptoUuid $conditionId, Collection &$entityMapping): ?AptoUuid
    {
        $newConditionId = $this->nextConditionId();
        $ordCondition = $this->getCondition($conditionId);
        $copiedCondition = $ordCondition->copy($newConditionId, $entityMapping);
        $this->conditions->set($newConditionId->getId(), $copiedCondition);

        return $copiedCondition->getId();
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    private function hasCondition(AptoUuid $id): bool
    {
        return $this->conditions->containsKey($id->getId());
    }

    /**
     * @return AptoUuid
     */
    private function nextConditionId(): AptoUuid
    {
        return new AptoUuid();
    }
}
