<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Service\Math\Calculator;

class ElementRangeValue implements ElementValue, EffectiveElementValue
{

    /**
     * @var float|ComputedValueReference
     */
    protected $minimum;

    /**
     * @var float|ComputedValueReference
     */
    protected $maximum;

    /**
     * @var float
     */
    protected $step;

    /**
     * ElementRangeValue constructor.
     * @param float|ComputedValueReference $minimum
     * @param float|ComputedValueReference $maximum
     * @param float $step
     */
    public function __construct($minimum = 0.0, $maximum = 0.0, float $step = 1.0)
    {
        $this->assertValidBound($minimum, 'minimum');
        $this->assertValidBound($maximum, 'maximum');

        $this->minimum = is_int($minimum) ? (float) $minimum : $minimum;
        $this->maximum = is_int($maximum) ? (float) $maximum : $maximum;
        $this->step = $step;

        $this->assertMinimumLessOrEqualMaximum();
        $this->assertValidStep();
    }

    /**
     * @return float
     */
    public function getMinimum(): float
    {
        $this->assertEffective();
        return $this->minimum;
    }

    /**
     * @return float
     */
    public function getMaximum(): float
    {
        $this->assertEffective();
        return $this->maximum;
    }

    /**
     * @return float
     */
    public function getStep(): float
    {
        return $this->step;
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
            'json' => [
                'minimum' => $this->encodeBound($this->minimum),
                'maximum' => $this->encodeBound($this->maximum),
                'step' => $this->step
            ]
        ];
    }

    /**
     * @param array $json
     * @return ElementValue
     */
    public static function jsonDecode(array $json): ElementValue
    {
        if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementRangeValue\' due to wrong class namespace.');
        }
        if (!isset($json['json']['minimum']) || !isset($json['json']['maximum']) || !isset($json['json']['step'])) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementRangeValue\' due to missing values.');
        }

        return new self(
            self::decodeBound($json['json']['minimum']),
            self::decodeBound($json['json']['maximum']),
            (float) $json['json']['step']
        );
    }

    /**
     * @param $bound
     * @param string $label
     */
    private function assertValidBound($bound, string $label): void
    {
        if (!is_float($bound) && !is_int($bound) && !($bound instanceof ComputedValueReference)) {
            throw new \InvalidArgumentException(
                'The given ' . $label . ' must be a number or an instance of ComputedValueReference.'
            );
        }
    }

    /**
     * The minimum set must be less or equal the maximum
     */
    protected function assertMinimumLessOrEqualMaximum()
    {
        if (is_float($this->minimum) && is_float($this->maximum) && $this->minimum > $this->maximum) {
            throw new \InvalidArgumentException('The given minimum must be less or equal the given maximum.');
        }
    }

    /**
     * The step must be greater than zero
     */
    protected function assertValidStep()
    {
        if ($this->step <= 0) {
            throw new \InvalidArgumentException('The given step must be greater than zero.');
        }
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueLowerThan($value)
    {
        $this->assertEffective();

        if (is_array($value)) {
            return null;
        }

        $value = (float)$value;

        // value outside of range
        if ($value < $this->minimum || $value > $this->maximum) {
            return null;
        }

        $offset = $value - $this->minimum;
        if (self::modulo((string) $offset, (string) $this->step) == 0) {
            $offset -= $this->step;
        } else {
            $offset -= (float) self::modulo((string) $offset, (string) $this->step);
        }

        $result = $this->minimum + $offset;
        if ($result < $this->minimum || $result > $this->maximum) {
            return null;
        }

        return $result;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueGreaterThan($value)
    {
        $this->assertEffective();

        if (is_array($value)) {
            return null;
        }

        $value = (float)$value;

        // value outside of range
        if ($value < $this->minimum || $value > $this->maximum) {
            return null;
        }

        $offset = $value - $this->minimum;
        if (self::modulo((string) $offset, (string) $this->step) == 0) {
            $offset += $this->step;
        } else {
            $offset -= (float) self::modulo((string) $offset, (string) $this->step) - $this->step;
        }

        $result = $this->minimum + $offset;
        if ($result < $this->minimum || $result > $this->maximum) {
            return null;
        }

        return $result;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueEqualTo($value)
    {
        $this->assertEffective();

        if (is_array($value)) {
            return null;
        }

        $value = (float)$value;

        // value outside of range
        if ($value < $this->minimum || $value > $this->maximum) {
            return null;
        }

        $offset = $value - $this->minimum;
        if (self::modulo((string) $offset, (string) $this->step) != 0) {
            return null;
        }

        return $value;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueNotEqualTo($value)
    {
        $this->assertEffective();

        $min = $this->getValueLowerThan($value);
        if (null !== $min) {
            return $min;
        }

        $max = $this->getValueGreaterThan($value);
        if (null !== $max) {
            return $max;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function getAnyValue()
    {
        if (!$this->isEffective()) {
            return null;
        }

        return $this->minimum;
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
        $this->assertEffective();

        if (is_array($value)) {
            return false;
        }

        // calculator instance
        $calculator = new Calculator();

        // values to string
        $value = (string) $value;
        $min = (string) $this->minimum;
        $max = (string) $this->maximum;
        $step = (string) $this->step;

        // value outside of range
        if ($calculator->lt($value, $min) || $calculator->gt($value, $max)) {
            return false;
        }

        $offset = $calculator->sub($value, $min);
        $mod = $calculator->mod($offset, $step);

        return $calculator->eq($mod, '0');
    }

    /**
     * @param string $a
     * @param string $b
     * @return string
     */
    protected static function modulo(string $a, string $b)
    {
        // calculator instance
        $calculator = new Calculator();

        // calculate modulo: ($a - $b * floor($a / $b))
        return $calculator->sub(
            $a, $calculator->mul(
            $b, $calculator->floor(
            $calculator->div($a, $b)
        )
        )
        );
    }

    /**
     * @return bool
     */
    public function isEffective(): bool
    {
        return !($this->minimum instanceof ComputedValueReference)
            && !($this->maximum instanceof ComputedValueReference);
    }

    /**
     * @param array $computedValues
     * @return ElementValue
     */
    public function withEffectiveValues(array $computedValues): ElementValue
    {
        return new self(
            $this->resolveBound($this->minimum, $computedValues, 'minimum'),
            $this->resolveBound($this->maximum, $computedValues, 'maximum'),
            $this->step
        );
    }

    /**
     * @param $bound
     * @param array $computedValues
     * @param string $label
     * @return float
     */
    private function resolveBound($bound, array $computedValues, string $label): float
    {
        if (!($bound instanceof ComputedValueReference)) {
            return (float) $bound;
        }

        $name = $bound->getName();
        if (!array_key_exists($name, $computedValues)) {
            throw new \InvalidArgumentException(
                'Cannot resolve ' . $label . ': computed value \'' . $name . '\' was not found.'
            );
        }

        return (float) $computedValues[$name];
    }

    /**
     * @param $bound
     */
    private function assertEffective(): void
    {
        if (!$this->isEffective()) {
            throw new \LogicException(
                'ElementRangeValue must be resolved via withEffectiveValues() before it can be evaluated.'
            );
        }
    }

    /**
     * @param float|ComputedValueReference $bound
     * @return array
     */
    private function encodeBound($bound): array
    {
        if ($bound instanceof ComputedValueReference) {
            return [
                'type' => 'computed',
                'name' => $bound->getName()
            ];
        }

        return [
            'type' => 'fixed',
            'value' => (float) $bound
        ];
    }

    /**
     * @param mixed $raw
     * @return float|ComputedValueReference
     */
    public static function decodeBound($raw)
    {
        if (!is_array($raw)) {
            return (float) $raw;
        }

        if (($raw['type'] ?? null) === 'computed') {
            if (!isset($raw['name']) || !is_string($raw['name'])) {
                throw new \InvalidArgumentException('Computed value bound requires a string name.');
            }

            return new ComputedValueReference($raw['name']);
        }

        if (($raw['type'] ?? null) === 'fixed' && array_key_exists('value', $raw)) {
            return (float) $raw['value'];
        }

        throw new \InvalidArgumentException('Bound must be a number or a valid computed-value reference.');
    }

    /**
     * @return array
     */
    function jsonSerialize(): array
    {
        return [
            'type' => 'range',
            'minimum' => $this->serializeBound($this->minimum),
            'maximum' => $this->serializeBound($this->maximum),
            'step' => $this->getStep(),
        ];
    }

    /**
     * @param float|ComputedValueReference $bound
     * @return float|array
     */
    private function serializeBound($bound)
    {
        if ($bound instanceof ComputedValueReference) {
            return [
                'type' => 'computed',
                'name' => $bound->getName()
            ];
        }

        return $bound;
    }
}
