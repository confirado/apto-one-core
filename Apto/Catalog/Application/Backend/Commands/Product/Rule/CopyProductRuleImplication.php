<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

class CopyProductRuleImplication extends ProductRuleCommand
{
    /**
     * @var string
     */
    private string $implicationId;

    /**
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
