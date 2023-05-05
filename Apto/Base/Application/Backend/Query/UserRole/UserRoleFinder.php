<?php

namespace Apto\Base\Application\Backend\Query\UserRole;

use Apto\Base\Application\Core\Query\AptoFinder;

interface UserRoleFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $searchString
     * @return array
     */
    public function findUserRoles(string $searchString = ''): array;

    /**
     * @param array $directUserRoles
     * @return array
     */
    public function findInheritedUserRoles(array $directUserRoles): array;
}