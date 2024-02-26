<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

class UpdateProductRuleImplication extends AddProductRuleCriterion
{
    /**
     * @var string
     */
    private string $implicationId;

    /**
     * @param string      $productId
     * @param string      $ruleId
     * @param string      $implicationId
     * @param int         $type
     * @param string|null $computedValueId
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $property
     * @param int         $operator
     * @param string      $value
     */
    public function __construct(
        string $productId,
        string $ruleId,
        string $implicationId,
        int $type,
        string $computedValueId = null,
        string $sectionId = null,
        string $elementId = null,
        string $property = null,
        int $operator,
        string $value
    ) {
        parent::__construct($productId, $ruleId, $type, $sectionId, $elementId, $property, $computedValueId, $operator, $value);

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
