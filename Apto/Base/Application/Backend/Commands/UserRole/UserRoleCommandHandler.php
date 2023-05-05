<?php

namespace Apto\Base\Application\Backend\Commands\UserRole;

use Doctrine\Common\Collections\ArrayCollection;
use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Backend\Model\UserRole\UserRole;
use Apto\Base\Domain\Backend\Model\UserRole\UserRoleIdentifier;
use Apto\Base\Domain\Backend\Model\UserRole\UserRoleRepository;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Backend\Model\UserRole\InvalidUserRoleIdentifierException;

class UserRoleCommandHandler implements CommandHandlerInterface
{
    /**
     * @var UserRoleRepository
     */
    protected $userRoleRepository;

    /**
     * UserRoleCommandHandler constructor.
     * @param UserRoleRepository $userRoleRepository
     */
    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @param AddUserRole $command
     * @return void
     * @throws InvalidUserRoleIdentifierException
     * @throws InvalidUuidException
     */
    public function handleAddUserRole(AddUserRole $command)
    {
        $this->checkUniqueConstraints($command->getIdentifier());

        $userRole = new UserRole(
            $this->userRoleRepository->nextIdentity(),
            new UserRoleIdentifier($command->getIdentifier()),
            $command->getName()
        );

        $userRole->setChildren($this->buildUserRoleCollection($command->getChildren()));

        $this->userRoleRepository->add($userRole);
        $userRole->publishEvents();
    }

    /**
     * @param UpdateUserRole $command
     * @return void
     * @throws InvalidUserRoleIdentifierException
     */
    public function handleUpdateUserRole(UpdateUserRole $command)
    {
        $this->checkUniqueConstraints($command->getIdentifier(), $command->getId());

        $userRole = $this->userRoleRepository->findById($command->getId());

        $userRole
            ->setIdentifier(new UserRoleIdentifier($command->getIdentifier()))
            ->setName($command->getName())
            ->setChildren($this->buildUserRoleCollection($command->getChildren()));

        $this->userRoleRepository->update($userRole);
        $userRole->publishEvents();
    }

    /**
     * @param RemoveUserRole $command
     */
    public function handleRemoveUserRole(RemoveUserRole $command)
    {
        $userRole = $this->userRoleRepository->findById($command->getId());
        $this->userRoleRepository->remove($userRole);

        // @todo: publish DomainEvents if userRoles exists
    }

    /**
     * @param string $identifier
     * @param $id
     * @return void
     * @throws UserRoleAlreadyExistsException
     */
    protected function checkUniqueConstraints(string $identifier, $id = null)
    {
        $userRoleExists = $this->userRoleRepository->findOneByIdentifier($identifier);

        if(null !== $userRoleExists) {
            if(null === $id) {
                $this->throwUserRoleAlreadyExistException($identifier);
            }

            if($userRoleExists->getId()->getId() !== $id) {
                $this->throwUserRoleAlreadyExistException($identifier);
            }
        }
    }

    /**
     * @param string $identifier
     * @throws UserRoleAlreadyExistsException
     */
    protected function throwUserRoleAlreadyExistException(string $identifier)
    {
        throw new UserRoleAlreadyExistsException('A user-role with an identifier ' . $identifier . '\' already exists.');
    }

    /**
     * @param array $userRoles
     * @return ArrayCollection
     */
    protected function buildUserRoleCollection(array $userRoles): ArrayCollection
    {
        $userRoleCollection = new ArrayCollection();
        foreach ($userRoles as $userRole) {
            $userRoleModel = $this->userRoleRepository->findById($userRole['id']);
            $userRoleCollection->add($userRoleModel);
        }

        return $userRoleCollection;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddUserRole::class => [
            'method' => 'handleAddUserRole',
            'bus' => 'command_bus'
        ];

        yield UpdateUserRole::class => [
            'method' => 'handleUpdateUserRole',
            'bus' => 'command_bus'
        ];

        yield RemoveUserRole::class => [
            'method' => 'handleRemoveUserRole',
            'bus' => 'command_bus'
        ];
    }
}