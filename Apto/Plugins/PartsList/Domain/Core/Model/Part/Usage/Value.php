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
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
