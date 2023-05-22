<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

class ElementBoolValue implements ElementValue
{

    /**
     * ElementBoolValue constructor.
     */
    public function __construct()
    {
        // nothing to do, yet
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
            'json' => []
        ];
    }

    /**
     * @param array $json
     * @return ElementValue
     */
    public static function jsonDecode(array $json): ElementValue
    {
        if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementBoolValue\' due to wrong class namespace.');
        }

        return new self();
    }


    /**
     * @param float $value
     * @return mixed|null
     */
    public function getValueLowerThan($value)
    {
        return null;
    }

    /**
     * @param float $value
     * @return mixed|null
     */
    public function getValueGreaterThan($value)
    {
        return null;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueEqualTo($value)
    {
        return ($value === true || $value === false) ? $value : null;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueNotEqualTo($value)
    {
        return ($value === true || $value === false) ? !$value : false;
    }

    /**
     * @return mixed|null
     */
    public function getAnyValue()
    {
        return false;
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
        return $value === true || $value === false;
    }

    /**
     * @return array
     */
    function jsonSerialize(): array
    {
        return [
            'type' => 'bool'
        ];
    }
}
