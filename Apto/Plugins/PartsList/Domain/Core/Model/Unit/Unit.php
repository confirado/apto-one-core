<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Unit;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class Unit extends AptoAggregate
{
    /**
     * @var string
     */
    private $unit;

    /**
     * Unit constructor.
     * @param AptoUuid $id
     * @param string $unit
     */
    public function __construct(AptoUuid $id, string $unit)
    {
        parent::__construct($id);
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return Unit
     */
    public function setUnit(string $unit): Unit
    {
        $this->unit = $unit;
        return $this;
    }
}