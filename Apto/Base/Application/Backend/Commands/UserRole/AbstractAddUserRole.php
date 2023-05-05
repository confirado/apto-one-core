<?php

namespace Apto\Base\Application\Backend\Commands\UserRole;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddUserRole implements CommandInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $children;

    /**
     * AddUserRole constructor.
     * @param string $identifier
     * @param string $name
     * @param array $children
     */
    public function __construct(string $identifier, string $name, array $children)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->children = $children;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}