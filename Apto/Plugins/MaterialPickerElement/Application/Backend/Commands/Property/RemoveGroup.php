<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\CommandInterface;

class RemoveGroup implements CommandInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * RemoveGroup constructor.
     * @param $id
     */
    public function __construct($id)
    {
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