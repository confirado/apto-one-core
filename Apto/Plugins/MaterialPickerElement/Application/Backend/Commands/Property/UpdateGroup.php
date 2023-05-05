<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

class UpdateGroup extends AbstractAddGroup
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateGroup constructor.
     * @param string $id
     * @param array $name
     * @param bool $allowMultiple
     */
    public function __construct(string $id, array $name, bool $allowMultiple)
    {
        parent::__construct($name, $allowMultiple);
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