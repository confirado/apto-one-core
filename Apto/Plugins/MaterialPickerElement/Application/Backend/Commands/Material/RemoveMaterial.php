<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class RemoveMaterial implements CommandInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * RemoveMaterial constructor.
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