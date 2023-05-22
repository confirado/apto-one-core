<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

class ElementJsonValue implements ElementValue
{
    /**
     * ElementJsonValue constructor.
     */
    public function __construct()
    {
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementTextValue\' due to wrong class namespace.');
        }

        return new self();
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueLowerThan($value)
    {
        return '';
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueGreaterThan($value)
    {
        return '';
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueEqualTo($value)
    {
        return '';
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueNotEqualTo($value)
    {
        return '';
    }

    /**
     * @return mixed|null
     */
    public function getAnyValue()
    {
        return '';
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
        try {
            $value = @json_encode($value, JSON_UNESCAPED_UNICODE);
            return $value !== false;
        }
        /** @phpstan-ignore-next-line  */
        catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return array
     */
    function jsonSerialize(): array
    {
        return [
            'type' => 'json'
        ];
    }
}
