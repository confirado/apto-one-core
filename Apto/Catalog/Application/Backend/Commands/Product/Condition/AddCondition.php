<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

class AddCondition extends AddProductCriterion
{
    /**
     * @var string
     */
    private string $identifier;

    /**
     * @param string $productId
     * @param string $identifier
     * @param int|null $type
     * @param int $operator
     * @param string $value
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $property
     * @param string|null $computedValueId
     */
        public function __construct(
        string      $productId,
        string      $identifier,
        ?int         $type,
        int         $operator,
        string      $value,
        string      $sectionId = null,
        string      $elementId = null,
        string      $property = null,
        string      $computedValueId = null,
    )
    {
        parent::__construct($productId, $type, $operator, $value, $sectionId, $elementId, $property, $computedValueId);

        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
