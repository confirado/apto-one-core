<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Security\FrontendUser;

use Throwable;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryBus;
use Apto\Base\Application\Backend\Query\FrontendUser\FindFrontendUserByEmail;
use Apto\Base\Application\Backend\Query\FrontendUser\FindFrontendUserByUsername;

class FrontendUserProvider implements UserProviderInterface
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
     * @return FrontendUser|UserInterface
     * @throws Throwable
     */
    public function loadUserByUsername($username)
    {
        // make a call to your webservice here
        $aptoUser = null;

        if(strpos($username, '@')){
            $readUser = new FindFrontendUserByEmail($username, false);
        } else{
            $readUser = new FindFrontendUserByUsername($username, false);
        }

        $this->queryBus->handle($readUser, $aptoUser);

        // pretend it returns an array on success, false if there is no user

        if ($aptoUser) {
            return FrontendUser::createFromBaseUser($aptoUser);
        }

        throw new UserNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    /**
     * @param UserInterface $user
     * @return FrontendUser|UserInterface
     * @throws Throwable
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof FrontendUser) {
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
        return $class === FrontendUser::class;
    }
}
