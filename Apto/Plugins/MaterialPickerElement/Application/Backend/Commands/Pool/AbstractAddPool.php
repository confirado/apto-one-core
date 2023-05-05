<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Pool;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddPool implements CommandInterface
{
    /**
     * @var array
     */
    private $name;

    /**
     * AddPool constructor.
     * @param array $name
     */
    public function __construct(array $name) {
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