<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Unit;

use Apto\Base\Application\Core\CommandInterface;

class AddUnit implements CommandInterface
{
    /**
     * @var string
     */
    private $unit;

    /**
     * AddUnit constructor.
     * @param string $unit
     */
    public function __construct(string $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }
}