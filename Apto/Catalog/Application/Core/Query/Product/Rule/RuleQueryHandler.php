<?php

namespace Apto\Catalog\Application\Core\Query\Product\Rule;

use Apto\Base\Application\Core\QueryHandlerInterface;

class RuleQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ProductRuleFinder
     */
    private $productRuleFinder;

    /**
     * RuleQueryHandler constructor.
     * @param ProductRuleFinder $productRuleFinder
     */
    public function __construct(ProductRuleFinder $productRuleFinder)
    {
        $this->productRuleFinder = $productRuleFinder;
    }

    /**
     * @param FindRule $query
     * @return array|null
     */
    public function handleFindRule(FindRule $query)
    {
        return $this->productRuleFinder->findById($query->getRuleId());
    }

    /**
     * @param FindRuleConditions $query
     * @return array|null
     */
    public function handleFindRuleConditions(FindRuleConditions $query)
    {
        return $this->productRuleFinder->findConditions($query->getRuleId());
    }

    /**
     * @param FindRuleCondition $query
     *
     * @return mixed
     */
    public function handleFindRuleCondition(FindRuleCondition $query)
    {
        return $this->productRuleFinder->findCondition($query->getRuleId(), $query->getConditionId());
    }

    /**
     * @param FindRuleImplications $query
     * @return array|null
     */
    public function handleFindRuleImplications(FindRuleImplications $query)
    {
        return $this->productRuleFinder->findImplications($query->getRuleId());
    }

    /**
     * @param FindRuleImplication $query
     *
     * @return mixed
     */
    public function handleFindRuleImplication(FindRuleImplication $query)
    {
        return $this->productRuleFinder->findImplication($query->getRuleId(), $query->getImplicationId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindRule::class => [
            'method' => 'handleFindRule',
            'bus' => 'query_bus'
        ];

        yield FindRuleConditions::class => [
            'method' => 'handleFindRuleConditions',
            'bus' => 'query_bus'
        ];

        yield FindRuleCondition::class => [
            'method' => 'handleFindRuleCondition',
            'bus' => 'query_bus'
        ];

        yield FindRuleImplications::class => [
            'method' => 'handleFindRuleImplications',
            'bus' => 'query_bus'
        ];

        yield FindRuleImplication::class => [
            'method' => 'handleFindRuleImplication',
            'bus' => 'query_bus'
        ];
    }
}
