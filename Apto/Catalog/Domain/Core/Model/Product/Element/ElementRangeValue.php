<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Service\Math\Calculator;

class ElementRangeValue implements ElementValue
{

    /**
     * @var float
     */
    protected $minimum;

    /**
     * @var float
     */
    protected $maximum;

    /**
     * @var float
     */
    protected $step;

    /**
     * ElementRangeValue constructor.
     * @param float $minimum
     * @param float $maximum
     * @param float $step
     */
    public function __construct(float $minimum = 0.0, float $maximum = 0.0, float $step = 1.0)
    {
        $this->minimum = $minimum;
        $this->maximum = $maximum;
        $this->step = $step;
        $this->assertMinimumLessOrEqualMaximum();
        $this->assertValidStep();
    }

    /**
     * @return float
     */
    public function getMinimum(): float
    {
        return $this->minimum;
    }

    /**
     * @return float
     */
    public function getMaximum(): float
    {
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
                'minimum' => $this->minimum,
                'maximum' => $this->maximum,
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
            (float)$json['json']['minimum'],
            (float)$json['json']['maximum'],
            (float)$json['json']['step']
        );
    }

    /**
     * The minimum set must be less or equal the maximum
     */
    protected function assertMinimumLessOrEqualMaximum()
    {
        if ($this->minimum > $this->maximum) {
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
        if (is_array($value)) {
            return null;
        }

        $value = (float)$value;

        // value outside of range
        if ($value < $this->minimum || $value > $this->maximum) {
            return null;
        }

        $offset = $value - $this->minimum;
        if (self::modulo($offset, $this->step) == 0) {
            $offset -= $this->step;
        } else {
            $offset -= self::modulo($offset, $this->step);
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
        if (is_array($value)) {
            return null;
        }

        $value = (float)$value;

        // value outside of range
        if ($value < $this->minimum || $value > $this->maximum) {
            return null;
        }

        $offset = $value - $this->minimum;
        if (self::modulo($offset, $this->step) == 0) {
            $offset += $this->step;
        } else {
            $offset -= self::modulo($offset, $this->step) - $this->step;
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
        if (is_array($value)) {
            return null;
        }

        $value = (float)$value;

        // value outside of range
        if ($value < $this->minimum || $value > $this->maximum) {
            return null;
        }

        $offset = $value - $this->minimum;
        if (self::modulo($offset, $this->step) != 0) {
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
        return $this->minimum;
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
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
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'type' => 'range',
            'minimum' => $this->getMinimum(),
            'maximum' => $this->getMaximum(),
            'step' => $this->getStep(),
        ];
    }
}