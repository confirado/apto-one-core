<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

use Apto\Base\Domain\Core\Service\Math\Calculator;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

class QuantityCalculation
{
    const OPERATIONS = ['add', 'sub', 'mul', 'div'];
    const FIELD_TYPES = ['selectable', 'computable'];
    const FIELD_POSITIONS = ['right', 'left'];

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $operation;

    /**
     * @var string
     */
    private $fieldType;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $fieldPosition;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * ElementUsageCalculation constructor.
     * @param bool $active
     * @param string|null $operation
     * @param string|null $fieldType
     * @param string|null $field
     * @param string|null $fieldPosition
     */
    public function __construct(bool $active, ?string $operation, ?string $fieldType, ?string $field, ?string $fieldPosition)
    {
        if (null !== $operation && !in_array($operation, self::OPERATIONS)) {
            throw new \InvalidArgumentException(
                'Invalid value for parameter operation. Operation must be one of (' . implode(',', self::OPERATIONS) . ')'
            );
        }

        if (null !== $fieldType && !in_array($fieldType, self::FIELD_TYPES)) {
            throw new \InvalidArgumentException(
                'Invalid value for parameter fieldType. FieldType must be one of (' . implode(',', self::FIELD_TYPES) . ')'
            );
        }

        if (null !== $fieldPosition && !in_array($fieldPosition, self::FIELD_POSITIONS)) {
            throw new \InvalidArgumentException(
                'Invalid value for parameter fieldPosition. FieldPosition must be one of (' . implode(',', self::FIELD_POSITIONS) . ')'
            );
        }

        $this->active = $active;
        $this->operation = $operation;
        $this->fieldType = $fieldType;
        $this->field = $field;
        $this->fieldPosition = $fieldPosition;
    }

    /**
     * @param ElementDefinition $elementDefinition
     * @param array $selectedValues
     * @param Quantity $quantity
     * @return Quantity
     */
    public function getCalculatedQuantity(ElementDefinition $elementDefinition, array $selectedValues, Quantity $quantity): Quantity
    {
        // init calculator
        $this->calculator = new Calculator();

        // return quantity if a required property is missing
        if (
            false === $this->active ||
            null === $this->operation ||
            null === $this->fieldType ||
            null === $this->field ||
            null === $this->fieldPosition
        ) {
            return $quantity;
        }

        // get field value
        $fieldValue = $this->getFieldValue($elementDefinition, $selectedValues);
        if (null === $fieldValue) {
            return $quantity;
        }

        // get calculation values
        $calculationValues = $this->getCalculationValues($quantity, $fieldValue);
        if (null === $calculationValues['a'] || null === $calculationValues['b']) {
            return $quantity;
        }

        // get calculated value
        $calculatedValue = $this->getCalculatedValue($calculationValues);
        if (null === $calculatedValue) {
            return $quantity;
        }

        // return new quantity
        return new Quantity(
            $calculatedValue
        );
    }

    /**
     * @param ElementDefinition $elementDefinition
     * @param array $selectedValues
     * @return string|null
     */
    private function getFieldValue(ElementDefinition $elementDefinition, array $selectedValues): ?string
    {
        $fieldValue = null;
        switch ($this->fieldType) {
            case 'selectable': {
                if (array_key_exists($this->field, $selectedValues)) {
                    $fieldValue = $selectedValues[$this->field];
                }
                break;
            }
            case 'computable': {
                $computedValues = $elementDefinition->getComputableValues($selectedValues);
                if (array_key_exists($this->field, $computedValues)) {
                    $fieldValue = $computedValues[$this->field];
                }
                break;
            }
        }

        if (filter_var($fieldValue, FILTER_VALIDATE_FLOAT) === false) {
            return null;
        }

        return $fieldValue;
    }

    /**
     * @param Quantity $quantity
     * @param string $fieldValue
     * @return array|null
     */
    private function getCalculationValues(Quantity $quantity, string $fieldValue): ?array
    {
        $a = null;
        $b = null;

        switch ($this->fieldPosition) {
            case 'right': {
                $a = $quantity->getQuantity();
                $b = $fieldValue;
                break;
            }
            case 'left': {
                $a = $fieldValue;
                $b = $quantity->getQuantity();
                break;
            }
        }

        return [
            'a' => $a,
            'b' => $b
        ];
    }

    /**
     * @param array $calculationValues
     * @return string|null
     */
    private function getCalculatedValue(array $calculationValues): ?string
    {
        $calculatedValue = null;
        switch ($this->operation) {
            case 'add': {
                $calculatedValue = $this->calculator->add($calculationValues['a'], $calculationValues['b']);
                break;
            }
            case 'sub': {
                $calculatedValue = $this->calculator->sub($calculationValues['a'], $calculationValues['b']);
                break;
            }
            case 'mul': {
                $calculatedValue = $this->calculator->mul($calculationValues['a'], $calculationValues['b']);
                break;
            }
            case 'div': {
                if ($this->calculator->eq($calculationValues['b'], '0')) {
                    return null;
                }
                $calculatedValue = $this->calculator->div($calculationValues['a'], $calculationValues['b']);
                break;
            }
        }

        return $calculatedValue;
    }

    /**
     * @param Quantity $quantity
     * @param int $precision
     * @return Quantity
     */
    private function roundQuantity(Quantity $quantity, $precision = 2): Quantity
    {
        return new Quantity(
            $this->calculator->round($quantity->getQuantity(), $precision)
        );
    }
}