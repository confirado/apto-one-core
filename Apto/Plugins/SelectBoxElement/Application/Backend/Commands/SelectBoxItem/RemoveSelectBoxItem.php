<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem;

use Apto\Base\Application\Core\CommandInterface;

class RemoveSelectBoxItem implements CommandInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * RemoveSelectBoxItem constructor.
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