<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoJsonSerializable;

class ElementValueCollection implements AptoJsonSerializable, \JsonSerializable
{
    /**
     * @var array
     */
    protected $collection;

    /**
     * ElementValueCollection constructor.
     * @param array $collection
     */
    public function __construct(array $collection = [])
    {
        $this->collection = [];

        foreach ($collection as $item) {
            if ($item instanceof ElementValue) {
                $this->collection[] = $item;
            }
        }
    }

    /**
     * @return array
     */
    public function getCollection(): array
    {
        return $this->collection;
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
        /** @var ElementValue $elementValue */
        foreach ($this->collection as $elementValue) {
            if ($elementValue->contains($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an unspecific valid value
     * @return mixed|null
     */
    public function getAnyValue()
    {
        /** @var ElementValue $value */
        foreach ($this->collection as $element) {
            $guess = $element->getAnyValue();
            if (null !== $guess) {
                return $guess;
            }
        }

        return null;
    }

    /**
     * Returns a valid value lower than the allowed value
     * @param mixed $value
     * @return null|string
     */
    public function getLowerValue($value)
    {
        $result = null;

        /** @var ElementValue $value */
        foreach ($this->collection as $element) {
            $guess = $element->getValueLowerThan($value);
            if (null !== $guess) {
                $result = null === $result ? $guess : max($result, $guess);
            }
        }

        return $result;
    }

    /**
     * Returns allowed value if contained in element's definition
     * @param mixed $value
     * @return null|string
     */
    public function getEqualValue($value)
    {
        /** @var ElementValue $value */
        foreach ($this->collection as $element) {
            $guess = $element->getValueEqualTo($value);
            if (null !== $guess) {
                return $guess;
            }
        }

        return null;
    }

    /**
     * Returns a valid value greater than the allowed value
     * @param mixed $value
     * @return null|string
     */
    public function getGreaterValue($value)
    {
        $result = null;

        /** @var ElementValue $value */
        foreach ($this->collection as $element) {
            $guess = $element->getValueGreaterThan($value);
            if (null !== $guess) {
                $result = null === $result ? $guess : min($result, $guess);
            }
        }

        return $result;
    }

    /**
     * Returns a valid value not equal to the allowed value
     * @param mixed $value
     * @return null|string
     */
    public function getNotEqualValue($value)
    {
        /** @var ElementValue $value */
        foreach ($this->collection as $element) {
            $guess = $element->getValueNotEqualTo($value);
            if (null !== $guess) {
                return $guess;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        $collection = [];

        /** @var ElementValue $item */
        foreach ($this->collection as $item) {
            $collection[] = $item->jsonEncode();
        }

        return [
            'class' => get_class($this),
            'json' => [
                'collection' => $collection
            ]
        ];
    }

    /**
     * @param array $json
     * @return ElementValueCollection
     */
    public static function jsonDecode(array $json)
    {
        if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementValueCollection\' due to wrong class namespace.');
        }
        if (!isset($json['json']['collection'])) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementValueCollection\' due to missing values.');
        }

        $collection = [];

        foreach ($json['json']['collection'] as $item) {
            try {
                if (class_exists($item['class'])) {
                    $fullClassName = '\\' . $item['class'];
                    /** @var ElementValue $fullClassName */
                    $object = $fullClassName::jsonDecode($item);
                    if (!($object instanceof ElementValue)) {
                        throw new \InvalidArgumentException('Instance of ElementValue expected, but \'' . $fullClassName . '\' given.');
                    }
                    $collection[] = $object;
                }
            }
            catch (\Exception $exception) {
                throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementValue\' due to unable to create instance.');
            }
        }

        return new self($collection);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $collection = [];

        foreach ($this->collection as $value) {
            /** @var ElementValue $value */
            $collection[] = $value->jsonSerialize();
        }

        return $collection;
    }
}