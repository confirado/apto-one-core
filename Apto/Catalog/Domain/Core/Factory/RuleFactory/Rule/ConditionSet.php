<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\RuleFactory;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class ConditionSet
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var Condition
     */
    private $conditions;

    /**
     * @param string $id
     * @param string $identifier
     * @param int $conditionsOperator
     * @param array $conditions
     * @throws InvalidUuidException
     */
    public function __construct(string $id, string $identifier, int $conditionsOperator, array $conditions)
    {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->conditions = new Condition(
            new LinkOperator($conditionsOperator),
            RuleFactory::criteriaFromArray($conditions)
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Check, if criteria are fulfilled by given state
     * @param State $state
     * @param RulePayload $rulePayload
     * @return bool
     */
    public function isFulfilled(State $state, RulePayload $rulePayload): bool
    {
        return $this->conditions->isFulfilled($state, $rulePayload);
    }
}
