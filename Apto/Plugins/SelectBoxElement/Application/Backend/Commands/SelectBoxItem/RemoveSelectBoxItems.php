<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem;

use Apto\Base\Application\Core\CommandInterface;

class RemoveSelectBoxItems implements CommandInterface
{
    /**
     * @var array
     */
    protected $ids;

    /**
     * RemoveSelectBoxItems constructor.
     * @param array $ids
     */
    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }
}