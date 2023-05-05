<?php

namespace Apto\Base\Application\Backend\Commands\UserRole;

class UpdateUserRole extends AbstractAddUserRole
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateUserRole constructor.
     * @param string $id
     * @param string $identifier
     * @param string $name
     * @param array $children
     */
    public function __construct(string $id, string $identifier, string $name, array $children)
    {
        parent::__construct($identifier, $name, $children);
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