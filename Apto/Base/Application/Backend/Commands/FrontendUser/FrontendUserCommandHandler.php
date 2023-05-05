<?php

namespace Apto\Base\Application\Backend\Commands\FrontendUser;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Service\PasswordEncoder;
use Apto\Base\Domain\Core\Model\Email;
use Apto\Base\Domain\Core\Model\EmailValidationException;
use Apto\Base\Domain\Core\Model\FrontendUser\FrontendUserRepository;
use Apto\Base\Domain\Core\Model\FrontendUser\FrontendUser;
use Apto\Base\Domain\Core\Model\FrontendUser\UserName;
use Apto\Base\Domain\Core\Model\FrontendUser\InvalidUserNameException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\Exception\PasswordValidationException;

class FrontendUserCommandHandler extends AbstractCommandHandler
{
    /**
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @var PasswordEncoder
     */
    protected $passwordEncoder;

    /**
     * FrontendUserCommandHandler constructor.
     * @param FrontendUserRepository $frontendUserRepository
     * @param PasswordEncoder $passwordEncoder
     */
    public function __construct(FrontendUserRepository $frontendUserRepository, PasswordEncoder $passwordEncoder)
    {
        $this->frontendUserRepository = $frontendUserRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param string $username
     * @param string $email
     * @param null $id
     * @throws UserAlreadyExistsException
     */
    protected function checkUniqueConstraints(string $username, string $email, $id = null)
    {
        // check unique user name
        $userNameExists = $this->frontendUserRepository->findOneByUsername($username);
        if (null !== $userNameExists) {
            if (null === $id) {
                $this->throwUserAlreadyExistException($username);
            }

            if ($userNameExists->getId()->getId() !== $id) {
                $this->throwUserAlreadyExistException($username);
            }
        }

        // check unique email
        $userEmailExists = $this->frontendUserRepository->findOneByEmail($email);
        if (null !== $userEmailExists) {
            if (null === $id) {
                $this->throwUserAlreadyExistException($email, 'email');
            }

            if ($userEmailExists->getId()->getId() !== $id) {
                $this->throwUserAlreadyExistException($email, 'email');
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
     * @param AddFrontendUser $command
     * @return void
     * @throws EmailValidationException
     * @throws InvalidUuidException
     * @throws PasswordValidationException
     * @throws UserAlreadyExistsException
     * @throws InvalidUserNameException
     */
    public function handleAddFrontendUser(AddFrontendUser $command)
    {
        $this->checkUniqueConstraints($command->getUsername(), $command->getEmail());

        $password = $this->passwordEncoder->encodePassword($command->getPlainPassword());
        $user = new FrontendUser(
            $this->frontendUserRepository->nextIdentity(),
            new UserName($command->getUsername()),
            $password,
            new Email($command->getEmail())
        );

        $user
            ->setActive($command->getActive())
            ->setExternalCustomerGroupId($command->getExternalCustomerGroupId());


        $this->frontendUserRepository->add($user);
        $user->publishEvents();
    }

    /**
     * @param RemoveFrontendUser $command
     */
    public function handleRemoveFrontendUser(RemoveFrontendUser $command)
    {
        $user = $this->frontendUserRepository->findById($command->getId());
        $this->frontendUserRepository->remove($user);

        // @todo publish DomainEvents if user exists
    }

    /**
     * @param UpdateFrontendUser $command
     * @return void
     * @throws EmailValidationException
     * @throws InvalidUserNameException
     * @throws PasswordValidationException
     * @throws UserAlreadyExistsException
     */
    public function handleUpdateFrontendUser(UpdateFrontendUser $command)
    {
        $frontendUser = $this->frontendUserRepository->findById($command->getId());

        if (null !== $frontendUser) {
            $this->checkUniqueConstraints($command->getUsername(), $command->getEmail(),$command->getId());

            $frontendUser
                ->setActive($command->getActive())
                ->setUsername(new UserName($command->getUsername()))
                ->setEmail(new Email($command->getEmail()))
                ->setExternalCustomerGroupId($command->getExternalCustomerGroupId());

            if ($command->getPlainPassword()) {
                $password = $this->passwordEncoder->encodePassword($command->getPlainPassword());
                $frontendUser->setPassword($password);
            }

            $this->frontendUserRepository->update($frontendUser);
            $frontendUser->publishEvents();
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddFrontendUser::class => [
            'method' => 'handleAddFrontendUser',
            'bus' => 'command_bus'
        ];

        yield RemoveFrontendUser::class => [
            'method' => 'handleRemoveFrontendUser',
            'bus' => 'command_bus'
        ];

        yield UpdateFrontendUser::class => [
            'method' => 'handleUpdateFrontendUser',
            'bus' => 'command_bus'
        ];
    }
}
