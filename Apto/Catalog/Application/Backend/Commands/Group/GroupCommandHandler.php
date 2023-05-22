<?php

namespace Apto\Catalog\Application\Backend\Commands\Group;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Catalog\Domain\Core\Model\Group\Group;
use Apto\Catalog\Domain\Core\Model\Group\GroupRemoved;
use Apto\Catalog\Domain\Core\Model\Group\GroupRepository;
use Apto\Catalog\Domain\Core\Model\Product\IdentifierNullable;

use Apto\Base\Domain\Core\Model\InvalidUuidException;

class GroupCommandHandler extends AbstractCommandHandler
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * GroupCommandHandler constructor.
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param AddGroup $command
     * @throws GroupIdentifierAlreadyExists
     * @throws InvalidUuidException
     */
    public function handleAddGroup(AddGroup $command)
    {
        $group = new Group(
            $this->groupRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName())
        );

        $identifier = new IdentifierNullable($command->getIdentifier());
        if (null !== $identifier->getValue()) {
            $this->checkUniqueConstraints($identifier);
        }

        $group
            ->setPosition(
                $command->getPosition()
            )
            ->setIdentifier(
                $identifier
            );

        $this->groupRepository->add($group);
        $group->publishEvents();
    }

    /**
     * @param UpdateGroup $command
     * @throws GroupIdentifierAlreadyExists
     */
    public function handleUpdateGroup(UpdateGroup $command)
    {
        $group = $this->groupRepository->findById($command->getId());

        $identifier = new IdentifierNullable($command->getIdentifier());
        if (null !== $identifier->getValue()) {
            $this->checkUniqueConstraints($identifier, $group->getId()->getId());
        }

        if(null !== $group) {
            $group
                ->setName(
                    $this->getTranslatedValue($command->getName())
                )
                ->setPosition(
                    $command->getPosition()
                )
                ->setIdentifier(
                    $identifier
                );

            $this->groupRepository->update($group);
            $group->publishEvents();
        }
    }

    /**
     * @param RemoveGroup $command
     */
    public function handleRemoveGroup(RemoveGroup $command)
    {
        $group = $this->groupRepository->findById($command->getId());

        if(null !== $group) {
            $this->groupRepository->remove($group);
            DomainEventPublisher::instance()->publish(
                new GroupRemoved(
                    $group->getId()
                )
            );
        }
    }

    /**
     * @param IdentifierNullable $identifier
     * @param string|null $id
     * @return void
     * @throws GroupIdentifierAlreadyExists
     */
    protected function checkUniqueConstraints(IdentifierNullable $identifier, string $id = null)
    {
        $groupAlreadyExists = $this->groupRepository->findByIdentifier($identifier);

        if (null !== $groupAlreadyExists) {
            if (null === $id) {
                throw new GroupIdentifierAlreadyExists('Group Identifier already set on Group width Id: ' . $groupAlreadyExists->getId()->getId() . '.');
            }

            if ($groupAlreadyExists->getId()->getId() !== $id) {
                throw new GroupIdentifierAlreadyExists('Group Identifier already set on Group width Id: ' . $groupAlreadyExists->getId()->getId() . '.');
            }
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddGroup::class => [
            'method' => 'handleAddGroup',
            'bus' => 'command_bus'
        ];

        yield UpdateGroup::class => [
            'method' => 'handleUpdateGroup',
            'bus' => 'command_bus'
        ];

        yield RemoveGroup::class => [
            'method' => 'handleRemoveGroup',
            'bus' => 'command_bus'
        ];
    }
}
