<?php

namespace Apto\Catalog\Application\Backend\Commands\Group;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddGroup implements CommandInterface
{
    /**
     * @var string|null
     */
    private $identifier;

    /**
     * @var array
     */
    private $name;

    /**
     * @var int
     */
    private $position;

    /**
     * AddGroup constructor.
     * @param array $name
     * @param int $position
     * @param string|null $identifier
     */
    public function __construct(array $name, int $position = 0, string $identifier = null)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->position = $position;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}