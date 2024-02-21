<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Rule;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Criterion;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Rule extends AptoEntity
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
     * @var bool
     */
    protected $active;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $conditionsOperator;

    /**
     * @var Collection
     */
    protected $conditions;

    /**
     * @var int
     */
    protected $implicationsOperator;

    /**
     * @var bool
     */
    protected $softRule;

    /**
     * @var Collection
     */
    protected $implications;

    /**
     * @var AptoTranslatedValue
     */
    protected $errorMessage;

    /**
     * @var string
     */
    protected string $description;

    /**
     * Rule constructor.
     * @param AptoUuid $id
     * @param Product $product
     * @param string $name
     */
    public function __construct(AptoUuid $id, Product $product, string $name)
    {
        parent::__construct($id);
        $this->product = $product;
        $this->active = false;
        $this->name = $name;
        $this->conditionsOperator = self::OPERATOR_AND;
        $this->conditions = new ArrayCollection();
        $this->implicationsOperator = self::OPERATOR_AND;
        $this->softRule = false;
        $this->implications = new ArrayCollection();
        $this->description = '';
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Rule
     */
    public function setActive(bool $active): Rule
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Rule
     */
    public function setName(string $name): Rule
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param AptoTranslatedValue $errorMessage
     * @return Rule
     */
    public function setErrorMessage(AptoTranslatedValue $errorMessage): Rule
    {
        $this->errorMessage = $errorMessage;
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
     * @return Rule
     * @throws RuleCriterionInvalidOperatorException
     */
    public function setConditionsOperator(int $operator): Rule
    {
        if (!in_array($operator, self::$validOperators)) {
            throw new RuleCriterionInvalidOperatorException('The operator \'' . $operator . '\' is invalid.');
        }
        $this->conditionsOperator = $operator;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getConditions(): Collection
    {
        return $this->conditions;
    }

    /**
     * @param RuleCriterionOperator $operator
     * @param int|null $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param string|null $value
     * @return $this
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidTypeException
     * @throws RuleCriterionInvalidValueException
     */
    public function addCondition(
        RuleCriterionOperator $operator,
        ?int $type = Criterion::TYPE,
        ?AptoUuid $sectionId = null,
        ?AptoUuid $elementId = null,
        ?string $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string $value = null
    ): Rule {
        $conditionId = $this->nextConditionId();
        $this->conditions->set(
            $conditionId->getId(),
            new RuleCondition(
                $conditionId,
                $this,
                $operator,
                $type,
                $sectionId,
                $elementId,
                $property,
                $computedProductValue,
                $value
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $id
     * @param string $value
     * @return $this
     */
    public function updateConditionValue(AptoUuid $id, string $value): Rule
    {
        $condition = $this->getCondition($id);

        if (null === $condition) {
            return $this;
        }

        $condition->setValue($value);

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @return Rule
     */
    public function removeCondition(AptoUuid $conditionId): Rule
    {
        if ($this->conditions->containsKey($conditionId->getId())) {
            $this->conditions->remove($conditionId->getId());
        }
        return $this;
    }

    /**
     * @return Rule
     */
    public function removeAllConditions(): Rule
    {
        /** @var RuleCondition $condition */
        foreach ($this->conditions as $condition) {
            $this->conditions->remove($condition->getId()->getId());
        }
        return $this;
    }

    /**
     * @return AptoUuid
     */
    private function nextConditionId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @return int
     */
    public function getImplicationsOperator(): int
    {
        return $this->implicationsOperator;
    }

    /**
     * @param int $operator
     * @return Rule
     */
    public function setImplicationsOperator(int $operator): Rule
    {
        if (!in_array($operator, self::$validOperators)) {
            throw new \InvalidArgumentException('The operator \'' . $operator . '\' is not valid.');
        }
        $this->implicationsOperator = $operator;
        return $this;
    }


    /**
     * @return bool
     */
    public function getSoftRule(): bool
    {
        return $this->softRule;
    }

    /**
     * @param bool $softRule
     * @return Rule
     */
    public function setSoftRule(bool $softRule): Rule
    {
        $this->softRule = $softRule;
        return $this;
    }


    /**
     * @return Collection
     */
    public function getImplications(): Collection
    {
        return $this->implications;
    }

    /**
     * @param RuleCriterionOperator $operator
     * @param int|null $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param string|null $value
     * @return $this
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidTypeException
     * @throws RuleCriterionInvalidValueException
     */
    public function addImplication(
        RuleCriterionOperator $operator,
        ?int $type = Criterion::TYPE,
        ?AptoUuid $sectionId = null,
        ?AptoUuid $elementId = null,
        ?string $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string $value = null
    ): Rule
    {
        $implicationId = $this->nextImplicationId();
        $this->implications->set(
            $implicationId->getId(),
            new RuleImplication(
                $implicationId,
                $this,
                $operator,
                $type,
                $sectionId,
                $elementId,
                $property,
                $computedProductValue,
                $value
            )
        );
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @param string $value
     * @return $this
     */
    public function updateImplicationValue(AptoUuid $id, string $value): Rule
    {
        $implication = $this->getImplication($id);

        if (null === $implication) {
            return $this;
        }

        $implication->setValue($value);

        return $this;
    }

    /**
     * @param AptoUuid $implicationId
     * @return Rule
     */
    public function removeImplication(AptoUuid $implicationId): Rule
    {
        if ($this->implications->containsKey($implicationId->getId())) {
            $this->implications->remove($implicationId->getId());
        }
        return $this;
    }

    /**
     * @return Rule
     */
    public function removeAllImplications(): Rule
    {
        /** @var RuleImplication $implication */
        foreach ($this->implications as $implication) {
            $this->implications->remove($implication->getId()->getId());
        }
        return $this;
    }

    /**
     * @return AptoUuid
     */
    private function nextImplicationId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @param AptoUuid $id
     * @param Collection $entityMapping
     * @return Rule
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidTypeException
     * @throws RuleCriterionInvalidValueException
     */
    public function copy(AptoUuid $id, Collection &$entityMapping): Rule
    {
        // create new rule
        $rule = new Rule(
            $id,
            $entityMapping->get($this->product->getId()->getId()),
            $this->getName()
        );

        // add new rule to entityMapping
        $entityMapping->set(
            $this->getId()->getId(),
            $rule
        );

        // set conditions
        $rule->conditions = $this->copyConditions($entityMapping);

        // set implications
        $rule->implications = $this->copyImplications($entityMapping);

        // set properties
        $rule
            ->setActive($this->getActive())
            ->setName($this->getName())
            ->setErrorMessage($this->getErrorMessage())
            ->setConditionsOperator($this->getConditionsOperator())
            ->setImplicationsOperator($this->getImplicationsOperator())
            ->setSoftRule($this->getSoftRule())
            ->setDescription($this->getDescription());

        // return new rule
        return $rule;
    }

    /**
     * @param Collection $entityMapping
     * @return Collection
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidTypeException
     * @throws RuleCriterionInvalidValueException
     */
    private function copyConditions(Collection &$entityMapping): Collection
    {
        $collection = new ArrayCollection();

        /** @var RuleCondition $condition */
        foreach ($this->conditions as $condition) {
            $conditionId = $this->nextConditionId();
            $copiedCondition = $condition->copy($conditionId, $entityMapping);

            if (null === $copiedCondition) {
                continue;
            }

            $collection->set(
                $conditionId->getId(),
                $copiedCondition
            );
        }

        return $collection;
    }

    /**
     * @param Collection $entityMapping
     * @return Collection
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidTypeException
     * @throws RuleCriterionInvalidValueException
     */
    private function copyImplications(Collection &$entityMapping): Collection
    {
        $collection = new ArrayCollection();

        /** @var RuleImplication $implication */
        foreach ($this->implications as $implication) {
            $implicationId = $this->nextImplicationId();
            $copiedImplication = $implication->copy($implicationId, $entityMapping);

            if (null === $copiedImplication) {
                continue;
            }

            $collection->set(
                $implicationId->getId(),
                $copiedImplication
            );
        }

        return $collection;
    }

    /**
     * @param AptoUuid $id
     * @return RuleCondition|null
     */
    private function getCondition(AptoUuid $id)
    {
        if ($this->hasCondition($id)) {
            return $this->conditions->get($id->getId());
        }

        return null;
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasCondition(AptoUuid $id): bool
    {
        return $this->conditions->containsKey($id->getId());
    }

    /**
     * @param AptoUuid $id
     * @return RuleImplication|null
     */
    private function getImplication(AptoUuid $id)
    {
        if ($this->hasImplication($id)) {
            return $this->implications->get($id->getId());
        }

        return null;
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasImplication(AptoUuid $id): bool
    {
        return $this->implications->containsKey($id->getId());
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): Rule
    {
        $this->description = $description;
        return $this;
    }
}
