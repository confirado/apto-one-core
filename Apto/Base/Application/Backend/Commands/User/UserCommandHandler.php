<?php

namespace Apto\Base\Application\Backend\Commands\User;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Backend\Model\User\InvalidUserNameException;
use Apto\Base\Domain\Backend\Model\User\User;
use Apto\Base\Domain\Backend\Model\User\UserName;
use Apto\Base\Domain\Backend\Model\User\UserRepository;
use Apto\Base\Domain\Backend\Model\UserRole\UserRoleRepository;
use Apto\Base\Domain\Core\Model\Email;
use Apto\Base\Domain\Core\Model\EmailValidationException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\Exception\PasswordValidationException;
use Apto\Base\Domain\Core\Service\PasswordEncoder;
use Doctrine\Common\Collections\ArrayCollection;

class UserCommandHandler implements CommandHandlerInterface
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserRoleRepository
     */
    protected $userRoleRepository;

    /**
     * @var PasswordEncoder
     */
    protected $passwordEncoder;

    /**
     * UserCommandHandler constructor.
     * @param UserRepository $userRepository
     * @param UserRoleRepository $userRoleRepository
     * @param PasswordEncoder $passwordEncoder
     */
    public function __construct(UserRepository $userRepository, UserRoleRepository $userRoleRepository, PasswordEncoder $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param AddUser $command
     * @return void
     * @throws EmailValidationException
     * @throws InvalidUserNameException
     * @throws InvalidUuidException
     * @throws PasswordValidationException
     * @throws UserAlreadyExistsException
     */
    public function handleAddUser(AddUser $command)
    {
        $this->checkUniqueConstraints($command->getUsername(), $command->getEmail(), $command->getApiKey());

        $password = $this->passwordEncoder->encodePassword($command->getPlainPassword());
        $user = new User(
            $this->userRepository->nextIdentity(),
            new UserName($command->getUsername()),
            $password,
            new Email($command->getEmail())
        );

        $user
            ->setActive($command->getActive())
            ->setRte($command->getRte())
            ->setApiKey($command->getApiKey())
            ->setApiOrigin($command->getApiOrigin());

        $userRoleCollection = $this->buildUserRoleCollection($command->getUserRoles());
        $user->setUserRoles($userRoleCollection);

        $this->userRepository->add($user);
        $user->publishEvents();
    }

    /**
     * @param UpdateUser $command
     * @return void
     * @throws EmailValidationException
     * @throws InvalidUserNameException
     * @throws PasswordValidationException
     * @throws UserAlreadyExistsException
     */
    public function handleUpdateUser(UpdateUser $command)
    {
        $user = $this->userRepository->findById($command->getId());

        if (null !== $user) {
            $this->checkUniqueConstraints($command->getUsername(), $command->getEmail(), $command->getApiKey(), $command->getId());

            $user
                ->setActive($command->getActive())
                ->setUsername(new UserName($command->getUsername()))
                ->setEmail(new Email($command->getEmail()))
                ->setUserRoles($this->buildUserRoleCollection($command->getUserRoles()))
                ->setRte($command->getRte())
                ->setApiKey($command->getApiKey())
                ->setApiOrigin($command->getApiOrigin());

            if ($command->getPlainPassword()) {
                $password = $this->passwordEncoder->encodePassword($command->getPlainPassword());
                $user->setPassword($password);
            }

            $this->userRepository->update($user);
            $user->publishEvents();
        }
    }

    /**
     * @param RemoveUser $command
     */
    public function handleRemoveUser(RemoveUser $command)
    {
        $user = $this->userRepository->findById($command->getId());
        $this->userRepository->remove($user);

        // @todo publish DomainEvents if user exists
    }

    /**
     * @param array $userRoles
     * @return ArrayCollection
     */
    protected function buildUserRoleCollection(array $userRoles)
    {
        $userRoleCollection = new ArrayCollection();
        foreach ($userRoles as $userRole) {
            $userRoleModel = $this->userRoleRepository->findById($userRole['id']);
            $userRoleCollection->add($userRoleModel);
        }

        return $userRoleCollection;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string|null $apiKey
     * @param string|null $id
     * @throws UserAlreadyExistsException
     */
    protected function checkUniqueConstraints(string $username, string $email, string $apiKey = null, ?string $id = null)
    {
        // check unique user name
        $userNameExists = $this->userRepository->findOneByUsername($username);
        if (null !== $userNameExists) {
            if (null === $id) {
                $this->throwUserAlreadyExistException($username);
            }

            if ($userNameExists->getId()->getId() !== $id) {
                $this->throwUserAlreadyExistException($username);
            }
        }

        // check unique email
        $userEmailExists = $this->userRepository->findOneByEmail($email);
        if (null !== $userEmailExists) {
            if (null === $id) {
                $this->throwUserAlreadyExistException($email, 'email');
            }

            if ($userEmailExists->getId()->getId() !== $id) {
                $this->throwUserAlreadyExistException($email, 'email');
            }
        }

        // check unique api key
        if (null === $apiKey) {
            return;
        }

        $userApiKeyExists = $this->userRepository->findOneByApiKey($apiKey);
        if (null !== $userApiKeyExists) {
            if (null === $id) {
                $this->throwUserAlreadyExistException($apiKey, 'apiKey');
            }

            if ($userApiKeyExists->getId()->getId() !== $id) {
                $this->throwUserAlreadyExistException($apiKey, 'apiKey');
            }
        }
    }

    /**
     * @param string $username
     * @param string $property
     * @throws UserAlreadyExistsException
     */
    protected function throwUserAlreadyExistException(string $username, $property = 'username')
    {
        throw new UserAlreadyExistsException('A user with an ' . $property . ' \'' . $username . '\' already exists.');
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddUser::class => [
            'method' => 'handleAddUser',
            'bus' => 'command_bus'
        ];

        yield UpdateUser::class => [
            'method' => 'handleUpdateUser',
            'bus' => 'command_bus'
        ];

        yield RemoveUser::class => [
            'method' => 'handleRemoveUser',
            'bus' => 'command_bus'
        ];
    }
}
