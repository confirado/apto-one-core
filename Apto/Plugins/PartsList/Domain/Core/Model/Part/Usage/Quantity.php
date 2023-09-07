<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

class Quantity
{
    /**
     * @var string
     */
    private $quantity;

    /**
     * Quantity constructor.
     * @param string $quantity
     */
    public function __construct(string $quantity)
    {
        $this->assertValidQuantity($quantity);
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
        //return rtrim(rtrim($this->quantity, '0'), '.');
    }

    /**
     * @param string $quantity
     */
    private function assertValidQuantity(string $quantity)
    {
        if (trim($quantity) === '') {
            throw new \InvalidArgumentException('Quantity cant be empty');
        }

        /*
        if (filter_var($quantity, FILTER_VALIDATE_FLOAT) === false) {
            $numberFromString = Number::fromString($quantity);
            if (!($numberFromString->isInteger() || $numberFromString->isDecimal())) {
                throw new \InvalidArgumentException('Amount must be an float(ish) value');
            }
        }*/
        //TODO: disabled for now, since formulas are to be allowed => need a way to assert a valid formula
    }
}
