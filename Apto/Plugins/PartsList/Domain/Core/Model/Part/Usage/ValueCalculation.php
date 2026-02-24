<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

class ValueCalculation
{
    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $field;

    /**
     * ElementUsageCalculation constructor.
     * @param bool $active
     * @param string|null $field
     */
    public function __construct(bool $active, ?string $field)
    {
        $this->active = $active;
        $this->field = $field;
    }

    /**
     * @param ElementDefinition $elementDefinition
     * @param array $selectedValues
     * @param Value $value
     * @return Value
     */
    public function getCalculatedValue(ElementDefinition $elementDefinition, array $selectedValues, Value $value): Value
    {
        // return value if a required property is missing
        if (
            false === $this->active ||
            null === $this->field
        ) {
            return $value;
        }

        // return new value
        return $value;
    }
}
