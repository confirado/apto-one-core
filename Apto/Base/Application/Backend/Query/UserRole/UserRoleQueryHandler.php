<?php

namespace Apto\Base\Application\Backend\Query\UserRole;

use Apto\Base\Application\Core\QueryHandlerInterface;

class UserRoleQueryHandler implements QueryHandlerInterface
{
    /**
     * @var UserRoleFinder
     */
    private $userRoleFinder;

    /**
     * UserRoleQueryHandler constructor.
     * @param UserRoleFinder $userRoleFinder
     */
    public function __construct(UserRoleFinder $userRoleFinder)
    {
        $this->userRoleFinder = $userRoleFinder;
    }

    /**
     * @param FindUserRole $query
     * @return array
     */
    public function handleFindUserRole(FindUserRole $query)
    {
        return $this->userRoleFinder->findById($query->getId());
    }

    /**
     * @param FindUserRoles $query
     * @return array
     */
    public function handleFindUserRoles(FindUserRoles $query)
    {
        return $this->userRoleFinder->findUserRoles($query->getSearchString());
    }

    /**
     * @param FindInheritedUserRoles $query
     * @return array
     */
    public function handleFindInheritedUserRoles(FindInheritedUserRoles $query)
    {
        return $this->userRoleFinder->findInheritedUserRoles($query->getDirectUserRoles());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindUserRole::class => [
            'method' => 'handleFindUserRole',
            'bus' => 'query_bus'
        ];

        yield FindUserRoles::class => [
            'method' => 'handleFindUserRoles',
            'bus' => 'query_bus'
        ];

        yield FindInheritedUserRoles::class => [
            'method' => 'handleFindInheritedUserRoles',
            'bus' => 'query_bus'
        ];
    }
}