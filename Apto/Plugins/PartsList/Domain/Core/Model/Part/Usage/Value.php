<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

class Value
{
    /**
     * @var string
     */
    private $value;

    /**
     * Value constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->assertValidValue($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
        //return rtrim(rtrim($this->value, '0'), '.');
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value)
    {
        /*
        if (trim($value) === '') {
            throw new \InvalidArgumentException('Value cant be empty');
        }
        */

        /*
        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            $numberFromString = Number::fromString($value);
            if (!($numberFromString->isInteger() || $numberFromString->isDecimal())) {
                throw new \InvalidArgumentException('Amount must be an float(ish) value');
            }
        }*/
        //TODO: disabled for now, since formulas are to be allowed => need a way to assert a valid formula
    }
}
