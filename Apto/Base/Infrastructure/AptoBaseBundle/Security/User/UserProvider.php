<?php
namespace Apto\Base\Infrastructure\AptoBaseBundle\Security\User;

use Throwable;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Apto\Base\Application\Backend\Query\User\FindUserByApiKey;
use Apto\Base\Application\Backend\Query\User\FindUserByUsername;
use Apto\Base\Application\Backend\Query\UserRole\FindInheritedUserRoles;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryBus;

class UserProvider implements UserProviderInterface
{
    /**
     * @var QueryBus
     */
    protected $queryBus;

    /**
     * UserProvider constructor.
     * @param QueryBus $queryBus
     */
    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * @param $username
     * @return User|UserInterface
     * @throws Throwable
     */
    public function loadUserByUsername($username)
    {
        // make a call to your webservice here
        $aptoUser = null;
        $readUser = new FindUserByUsername($username, false);
        $this->queryBus->handle($readUser, $aptoUser);

        // pretend it returns an array on success, false if there is no user
        if ($aptoUser) {
            return User::createFromBaseUser(
                $aptoUser,
                $this->findInheritedUserRoles($aptoUser)
            );
        }

        throw new UserNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    /**
     * @param $apiKey
     * @return User|UserInterface
     * @throws Throwable
     */
    public function loadUserByApiKey($apiKey)
    {
        // make a call to your webservice here
        $aptoUser = null;
        $readUser = new FindUserByApiKey($apiKey, false);
        $this->queryBus->handle($readUser, $aptoUser);

        // pretend it returns an array on success, false if there is no user
        if ($aptoUser) {
            return User::createFromBaseUser(
                $aptoUser,
                $this->findInheritedUserRoles($aptoUser)
            );
        }

        throw new UserNotFoundException(
            sprintf('Api Key "%s" does not exist.', $apiKey)
        );
    }

    /**
     * @param UserInterface $user
     * @return User|UserInterface
     * @throws Throwable
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param $class
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return $class === 'Apto\Base\Infrastructure\AptoBaseBundle\Security\User\User';
    }

    /**
     * @param array $aptoUser
     * @return array
     * @throws Throwable
     */
    private function findInheritedUserRoles(array $aptoUser): array
    {
        $aptoUserRoles = isset($aptoUser['userRoles']) ? $aptoUser['userRoles'] : [];
        $inheritedUserRoles = [];
        $readUserRoles = new FindInheritedUserRoles($aptoUserRoles);
        $this->queryBus->handle($readUserRoles, $inheritedUserRoles);

        return $inheritedUserRoles;
    }
}
