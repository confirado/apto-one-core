<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Condition;

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
}
