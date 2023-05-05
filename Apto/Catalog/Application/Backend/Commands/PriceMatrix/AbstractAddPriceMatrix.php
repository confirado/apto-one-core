<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddPriceMatrix implements CommandInterface
{
    /**
     * @var array
     */
    private $name;

    /**
     * AddPriceMatrix constructor.
     * @param array $name
     */
    public function __construct(array $name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }
}