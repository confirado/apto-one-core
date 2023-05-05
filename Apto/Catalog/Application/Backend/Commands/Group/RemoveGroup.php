<?php

namespace Apto\Catalog\Application\Backend\Commands\Group;

use Apto\Base\Application\Core\CommandInterface;

class RemoveGroup implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * RemoveGroup constructor.
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