<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

abstract class ProductRuleCommand extends ProductChildCommand
{
    /**
     * @var string
     */
    private $ruleId;

    /**
     * RemoveProductRuleCriterion constructor.
     * @param string $productId
     * @param string $ruleId
     */
    public function __construct(string $productId, string $ruleId)
    {
        parent::__construct($productId);
        $this->ruleId = $ruleId;
    }

    /**
     * @return string
     */
    public function getRuleId(): string
    {
        return $this->ruleId;
    }
}