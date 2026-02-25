<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class RuleUsage extends Usage
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
     * @var Collection
     */
    protected $conditions;

    /**
     * @var int
     */
    protected $conditionsOperator;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var string
     */
    protected $name;

    /**
     * RuleUsage constructor.
     * @param Part $part
     * @param AptoUuid $id
     * @param Quantity $quantity
     * @param string $name
     */
    public function __construct(Part $part, AptoUuid $id, Quantity $quantity, string $name)
    {
        parent::__construct($part, $id, $quantity);
        $this->conditions = new ArrayCollection();
        $this->active = false;
        $this->name = $name;
        $this->conditionsOperator = self::OPERATOR_AND;
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
     * @return RuleUsage
     */
    public function setConditionsOperator(int $operator): RuleUsage
    {
        if (!in_array($operator, self::$validOperators)) {
            throw new \InvalidArgumentException('The operator \'' . $operator . '\' is not valid.');
        }
        $this->conditionsOperator = $operator;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return RuleUsage
     */
    public function setActive(bool $active): RuleUsage
    {
        $this->active = $active;
        return $this;
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
     * @return RuleUsage
     */
    public function setName(string $name): RuleUsage
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param RuleCondition $condition
     * @return $this
     */
    public function addCondition(RuleCondition $condition): RuleUsage
    {
        $this->conditions->set(
            $condition->getId()->getId(),
            $condition
        );
        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @return RuleUsage
     */
    public function removeCondition(AptoUuid $conditionId): RuleUsage
    {
        if ($this->conditions->containsKey($conditionId->getId())) {
            $this->conditions->remove($conditionId->getId());
        }
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
     * @param AptoUuid $id
     * @return RuleCondition
     */
    public function getCondition(AptoUuid $id): RuleCondition
    {
        return $this->conditions->get($id->getId());
    }

    /**
     * @return AptoUuid
     */
    public function nextConditionId(): AptoUuid
    {
        return new AptoUuid();
    }
}
