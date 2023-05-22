<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

class ElementTextValue implements ElementValue
{
    /**
     * @var int
     */
    protected $minLength;

    /**
     * @var int
     */
    protected $maxLength;

    /**
     * ElementRangeValue constructor.
     * @param int $minLength
     * @param int $maxLength
     */
    public function __construct(int $minLength = 0, int $maxLength = 0)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->assertMinimumLessOrEqualMaximum();
    }

    /**
     * @return int
     */
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    /**
     * @return int
     */
    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
            'json' => [
                'minLength' => $this->minLength,
                'maxLength' => $this->maxLength,
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementTextValue\' due to wrong class namespace.');
        }
        if (!isset($json['json']['minLength']) || !isset($json['json']['maxLength'])) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementTextValue\' due to missing values.');
        }

        return new self(
            (int)$json['json']['minLength'],
            (int)$json['json']['maxLength']
        );
    }

    /**
     * The minimum set must be less or equal the maximum
     */
    protected function assertMinimumLessOrEqualMaximum()
    {
        if ($this->minLength > $this->maxLength) {
            throw new \InvalidArgumentException('The given minimum must be less or equal the given maximum.');
        }
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueLowerThan($value)
    {
        return ''; // @TODO
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueGreaterThan($value)
    {
        return ''; // @TODO
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueEqualTo($value)
    {
        return ''; // @TODO
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueNotEqualTo($value)
    {
        return ''; // @TODO
    }

    /**
     * @return mixed|null
     */
    public function getAnyValue()
    {
        return ''; // @TODO
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
        $valueLength = strlen($value);

        // value outside of range
        if ($valueLength < $this->minLength || $valueLength > $this->maxLength) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    function jsonSerialize(): array
    {
        return [
            'type' => 'text',
            'minLength' => $this->getMinLength(),
            'maxLength' => $this->getMaxLength()
        ];
    }
}
