<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Pool;

class UpdatePool extends AbstractAddPool
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdatePool constructor.
     * @param string $id
     * @param array $name
     */
    public function __construct(string $id, array $name)
    {
        parent::__construct($name);
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