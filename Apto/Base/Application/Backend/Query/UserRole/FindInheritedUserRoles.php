<?php

namespace Apto\Base\Application\Backend\Query\UserRole;

use Apto\Base\Application\Core\QueryInterface;

class FindInheritedUserRoles implements QueryInterface
{
    /**
     * @var array
     */
    private $directUserRoles;

    /**
     * @param array $directUserRoles
     */
    public function __construct(array $directUserRoles)
    {
        $this->directUserRoles = $directUserRoles;
    }

    /**
     * @return array
     */
    public function getDirectUserRoles(): array
    {
        return $this->directUserRoles;
    }
}