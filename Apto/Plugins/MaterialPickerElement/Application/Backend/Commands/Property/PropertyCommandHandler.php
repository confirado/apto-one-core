<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\Group;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\GroupRemoved;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\GroupRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\Property;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\PropertyRepository;

class PropertyCommandHandler extends AbstractCommandHandler
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var PropertyRepository
     */
    protected $propertyRepository;

    /**
     * @param GroupRepository $groupRepository
     * @param PropertyRepository $propertyRepository
     */
    public function __construct(GroupRepository $groupRepository, PropertyRepository $propertyRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->propertyRepository = $propertyRepository;
    }

    /**
     * @param AddGroup $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleAddGroup(AddGroup $command)
    {
        $group = new Group(
            $this->groupRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName())
        );
        $group->setAllowMultiple($command->getAllowMultiple());

        $this->groupRepository->add($group);
        $group->publishEvents();
    }

    /**
     * @param UpdateGroup $command
     * @return void
     */
    public function handleUpdateGroup(UpdateGroup $command)
    {
        $group = $this->groupRepository->findById($command->getId());
        $oldMultiple = $group->isAllowMultiple();

        if (null !== $group) {
            $group
                ->setName($this->getTranslatedValue($command->getName()))
                ->setAllowMultiple($command->getAllowMultiple());;

            $this->groupRepository->update($group);
            $group->publishEvents();
        }

        if ($oldMultiple && !$command->getAllowMultiple()) {
            $firstDefaultSkipped = false;
            /** @var Property $property */
            foreach ($group->getProperties() as $property) {
                if ($property->isDefault()) {
                    if ($firstDefaultSkipped) {
                        $property->setIsDefault(false);
                        $this->propertyRepository->update($property);
                        continue;
                    }
                    $firstDefaultSkipped = true;
                }
            }
        }
    }

    /**
     * @param RemoveGroup $command
     * @return void
     */
    public function handleRemoveGroup(RemoveGroup $command)
    {
        $group = $this->groupRepository->findById($command->getId());

        if (null !== $group) {
            $this->groupRepository->remove($group);
            DomainEventPublisher::instance()->publish(
                new GroupRemoved(
                    $group->getId()
                )
            );
        }
    }

    /**
     * @param AddGroupProperty $command
     * @return void
     */
    public function handleAddGroupProperty(AddGroupProperty $command)
    {
        $group = $this->groupRepository->findById($command->getGroupId());

        if (null !== $group) {
            $group->addProperty($this->getTranslatedValue($command->getPropertyName()));

            $this->groupRepository->update($group);
            $group->publishEvents();
        }
    }

    /**
     * @param UpdateProperty $command
     * @return void
     */
    public function handleUpdateProperty(UpdateProperty $command)
    {
        $property = $this->propertyRepository->findById($command->getId());

        if (null !== $property) {
            $property
                ->setName($this->getTranslatedValue($command->getName()));

            $this->propertyRepository->update($property);
            $property->publishEvents();
        }
    }

    /**
     * @param RemoveGroupProperty $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveGroupProperty(RemoveGroupProperty $command)
    {
        $group = $this->groupRepository->findById($command->getGroupId());

        if (null !== $group) {
            $group->removeProperty(
                new AptoUuid($command->getPropertyId())
            );

            $this->groupRepository->update($group);
            $group->publishEvents();
        }
    }

    /**
     * @param AddPropertyCustomProperty $command
     * @return void
     * @throws AptoCustomPropertyException
     */
    public function handleAddPropertyCustomProperty(AddPropertyCustomProperty $command)
    {
        $property = $this->propertyRepository->findById($command->getId());

        if (null !== $property) {
            $property->setCustomProperty(
                $command->getKey(),
                $command->getValue(),
                $command->getTranslatable()
            );

            $this->propertyRepository->update($property);
            $property->publishEvents();
        }
    }

    /**
     * @param RemovePropertyCustomProperty $command
     * @return void
     */
    public function handleRemovePropertyCustomProperty(RemovePropertyCustomProperty $command)
    {
        $property = $this->propertyRepository->findById($command->getPropertyId());

        if (null !== $property) {
            $property->removeCustomProperty(
                new AptoUuid($command->getId())
            );

            $this->propertyRepository->update($property);
            $property->publishEvents();
        }
    }

    /**
     * @param SetGroupPropertyIsDefault $command
     * @return void
     */
    public function handleSetGroupPropertyIsDefault(SetGroupPropertyIsDefault $command)
    {
        $group = $this->groupRepository->findById($command->getGroupId());
        if (null === $group) {
            return;
        }
        $property = $this->propertyRepository->findById($command->getPropertyId());
        if (null === $property) {
            return;
        }

        if (!$group->isAllowMultiple() && $command->isDefault() === true) {
            /** @var Property $groupProperty */
            foreach ($group->getProperties() as $groupProperty) {
                $groupProperty->setIsDefault(false);
                $this->propertyRepository->update($groupProperty);
            }
        }
        $property->setIsDefault($command->isDefault());
        $this->propertyRepository->update($property);
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddGroup::class => [
            'method' => 'handleAddGroup',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerGroup'
        ];

        yield UpdateGroup::class => [
            'method' => 'handleUpdateGroup',
            'bus' => 'command_bus',
            'aptoMessageName' => 'UpdateMaterialPickerGroup'
        ];

        yield RemoveGroup::class => [
            'method' => 'handleRemoveGroup',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerGroup'
        ];

        yield AddGroupProperty::class => [
            'method' => 'handleAddGroupProperty',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerGroupProperty'
        ];

        yield UpdateProperty::class => [
            'method' => 'handleUpdateProperty',
            'bus' => 'command_bus',
            'aptoMessageName' => 'UpdateMaterialPickerProperty'
        ];

        yield RemoveGroupProperty::class => [
            'method' => 'handleRemoveGroupProperty',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerGroupProperty'
        ];

        yield AddPropertyCustomProperty::class => [
            'method' => 'handleAddPropertyCustomProperty',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerPropertyCustomProperty'
        ];

        yield RemovePropertyCustomProperty::class => [
            'method' => 'handleRemovePropertyCustomProperty',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerPropertyCustomProperty'
        ];

        yield SetGroupPropertyIsDefault::class => [
            'method' => 'handleSetGroupPropertyIsDefault',
            'bus' => 'command_bus',
            'aptoMessageName' => 'SetMaterialPickerGroupPropertyIsDefault'
        ];
    }
}
