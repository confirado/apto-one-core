<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\PriceGroup;

use Apto\Base\Application\Core\CommandInterface;

class RemovePriceGroup implements CommandInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * RemovePriceGroup constructor.
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