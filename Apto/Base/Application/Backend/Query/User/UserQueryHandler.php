<?php

namespace Apto\Base\Application\Backend\Query\User;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Backend\Model\User\UserName;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;

class UserQueryHandler implements QueryHandlerInterface
{
    /**
     * @var UserFinder
     */
    private $userFinder;

    /**
     * @var string
     */
    private $saHash;

    /**
     * @param UserFinder $userFinder
     * @param AptoParameterInterface $aptoParameter
     */
    public function __construct(UserFinder $userFinder, AptoParameterInterface $aptoParameter)
    {
        $this->userFinder = $userFinder;

        if (!$aptoParameter->has('sa_hash')) {
            throw new \InvalidArgumentException('SA Hash must be available as parameter. Set "sa_hash" in your parameters.yml file.');
        }

        $this->saHash = $aptoParameter->get('sa_hash');
    }

    /**
     * @param FindUser $query
     * @return array
     */
    public function handleFindUser(FindUser $query)
    {
        if(strtolower($query->getId()) == UserName::USERNAME_SUPERUSER) {
            return $this->getSuperAdmin($query->getSecure());
        }
        return $this->userFinder->findById($query->getId(), $query->getSecure());
    }

    /**
     * @param FindUserByUsername $query
     * @return array
     */
    public function handleFindUserByUsername(FindUserByUsername $query)
    {
        if (strtolower($query->getUsername()) == UserName::USERNAME_SUPERUSER) {
            return $this->getSuperAdmin($query->getSecure());
        }
        return $this->userFinder->findByUsername($query->getUsername(), $query->getSecure());
    }

    /**
     * @param FindUserByApiKey $query
     * @return array
     */
    public function handleFindUserByApiKey(FindUserByApiKey $query)
    {
        return $this->userFinder->findByApiKey($query->getApiKey(), $query->getSecure());
    }

    /**
     * @param FindUserByApiOrigin $query
     * @return array
     */
    public function handleFindUserByApiOrigin(FindUserByApiOrigin $query)
    {
        return $this->userFinder->findByApiOrigin($query->getApiOrigin(), $query->getSecure());
    }

    /**
     * @param FindCurrentUser $query
     * @return array|null
     */
    public function handleFindCurrentUser(FindCurrentUser $query)
    {
        if (strtolower($query->getUsername()) == UserName::USERNAME_SUPERUSER) {
            return $this->getSuperAdmin();
        }
        return $this->userFinder->findByUsername($query->getUsername());
    }

    /**
     * @param FindUsers $query
     * @return array
     */
    public function handleFindUsers(FindUsers $query)
    {
        return $this->userFinder->findUsers($query->getSearchString(), $query->getSecure());
    }

    /**
     * @param FindUsersByUserIds $query
     * @return array
     */
    public function handleFindUsersByUserIds(FindUsersByUserIds $query)
    {
        return $this->userFinder->findFindUsersByUserIds($query->getUserIds(), $query->getSecure());
    }

    /**
     * @param bool $secure
     * @return array
     */
    private function getSuperAdmin($secure = true)
    {
        $superAdmin = [
            'id' => UserName::USERNAME_SUPERUSER,
            'username' => UserName::USERNAME_SUPERUSER,
            'salt' => '',
            'active' => true,
            'userRoles' => [
                ['identifier' => 'ROLE_SUPER_ADMIN']
            ]
        ];

        if ($secure === false) {
            $superAdmin['password'] = $this->saHash;
        }

        return $superAdmin;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindCurrentUser::class => [
            'method' => 'handleFindCurrentUser',
            'bus' => 'query_bus'
        ];

        yield FindUser::class => [
            'method' => 'handleFindUser',
            'bus' => 'query_bus'
        ];

        yield FindUserByApiKey::class => [
            'method' => 'handleFindUserByApiKey',
            'bus' => 'query_bus'
        ];

        yield FindUserByApiOrigin::class => [
            'method' => 'handleFindUserByApiOrigin',
            'bus' => 'query_bus'
        ];

        yield FindUserByUsername::class => [
            'method' => 'handleFindUserByUsername',
            'bus' => 'query_bus'
        ];

        yield FindUsers::class => [
            'method' => 'handleFindUsers',
            'bus' => 'query_bus'
        ];

        yield FindUsersByUserIds::class => [
            'method' => 'handleFindUsersByUserIds',
            'bus' => 'query_bus'
        ];
    }
}
