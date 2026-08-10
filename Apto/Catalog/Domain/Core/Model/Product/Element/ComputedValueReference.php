<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

final class ComputedValueReference
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Computed value reference name must not be empty.');
        }

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
