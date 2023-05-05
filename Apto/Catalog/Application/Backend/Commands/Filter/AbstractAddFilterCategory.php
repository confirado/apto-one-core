<?php

namespace Apto\Catalog\Application\Backend\Commands\Filter;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddFilterCategory implements CommandInterface
{
    /**
     * @var array
     */
    private $name;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var int
     */
    private $position;

    /**
     * AddFilterCategory constructor.
     * @param array $name
     * @param string $identifier
     * @param int $position
     */
    public function __construct(array $name, string $identifier, int $position)
    {
        $this->name = $name;
        $this->identifier = $identifier;
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
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
