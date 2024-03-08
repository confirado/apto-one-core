<?php

namespace Apto\Catalog\Application\Core\Query\Product\Rule;

class FindRuleImplication extends AbstractFindRule
{
    /**
     * @var string
     */
    private string $implicationId;

    /**
     * @param string $ruleId
     * @param string $implicationId
     */
    public function __construct(string $ruleId, string $implicationId)
    {
        parent::__construct($ruleId);
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
