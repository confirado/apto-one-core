<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Unit;

use Apto\Base\Application\Core\CommandInterface;

class RemoveUnit implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * RemoveUnit constructor.
     * @param string $id
     */
    public function __construct(string $id)
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