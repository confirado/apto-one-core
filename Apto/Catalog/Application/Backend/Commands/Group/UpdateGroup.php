<?php

namespace Apto\Catalog\Application\Backend\Commands\Group;

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
     * @param int $position
     * @param string|null $identifier
     */
    public function __construct(string $id, array $name, int $position = 0, string $identifier = null)
    {
        parent::__construct($name, $position, $identifier);
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