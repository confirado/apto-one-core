<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Unit;

class UpdateUnit extends AddUnit
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateUnit constructor.
     * @param string $id
     * @param string $unit
     */
    public function __construct(string $id, string $unit)
    {
        parent::__construct($unit);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}