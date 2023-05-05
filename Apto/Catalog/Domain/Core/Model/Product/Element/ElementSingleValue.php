<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

class ElementSingleValue implements ElementValue
{

    /**
     * @var float
     */
    protected $value;

    /**
     * ElementSingleValue constructor.
     * @param float $value
     */
    public function __construct(float $value = 0.0)
    {
        $this->value = $value;
        $this->assertValueNotNull();
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
            'json' => [
                'value' => $this->value
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementSingleValue\' due to wrong class namespace.');
        }
        if (!isset($json['json']['value'])) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementSingleValue\' due to missing values.');
        }

        return new self($json['json']['value']);
    }

    /**
     * The given value must not be empty
     */
    protected function assertValueNotNull()
    {
        if (null === $this->value) {
            throw new \InvalidArgumentException('The given value must not be null.');
        }
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueLowerThan($value)
    {
        return $this->value < $value ? $this->value : null;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueGreaterThan($value)
    {
        return !is_array($value) && $this->value > $value ? $this->value : null;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueEqualTo($value)
    {
        return !is_array($value) && $this->value == $value ? $this->value : null;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueNotEqualTo($value)
    {
        return !is_array($value) && $this->value != $value ? $this->value : null;
    }

    /**
     * @return mixed|null
     */
    public function getAnyValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
        return !is_array($value) && $this->value == $value;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'type' => 'single',
            'value' => $this->getValue()
        ];
    }
}