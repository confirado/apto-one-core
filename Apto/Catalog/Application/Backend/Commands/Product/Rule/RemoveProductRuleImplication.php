<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

class RemoveProductRuleImplication extends ProductRuleCommand
{
    /**
     * @var string
     */
    private $implicationId;

    /**
     * RemoveProductRuleCondition constructor.
     * @param string $productId
     * @param string $ruleId
     * @param string $implicationId
     */
    public function __construct(string $productId, string $ruleId, string $implicationId)
    {
        parent::__construct($productId, $ruleId);
        $this->implicationId = $implicationId;
    }

    /**
     * @return string
     */
    public function getImplicationId(): string
    {
        return $this->implicationId;
    }
}