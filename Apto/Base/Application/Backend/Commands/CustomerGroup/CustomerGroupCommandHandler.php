<?php

namespace Apto\Base\Application\Backend\Commands\CustomerGroup;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroup;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroupRemoved;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroupRepository;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;

class CustomerGroupCommandHandler extends AbstractCommandHandler
{
    /**
     * @var CustomerGroupRepository
     */
    private $customerGroupRepository;

    /**
     * CustomerGroupCommandHandler constructor.
     * @param CustomerGroupRepository $customerGroupRepository
     */
    public function __construct(CustomerGroupRepository $customerGroupRepository)
    {
        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * @param AddCustomerGroup $command
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function handleAddCustomerGroup(AddCustomerGroup $command)
    {
        if ($command->getFallback()) {
            $this->removeFallback($command->getShopId());
        }

        $customerGroup = new CustomerGroup(
            $this->customerGroupRepository->nextIdentity(),
            $command->getName(),
            $command->getInputGross(),
            $command->getShowGross(),
            new AptoUuid($command->getShopId()),
            $command->getExternalId()
        );

        $customerGroup->setFallback($command->getFallback());

        $this->customerGroupRepository->add($customerGroup);
        $customerGroup->publishEvents();
    }

    /**
     * @param UpdateCustomerGroup $command
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function handleUpdateCustomerGroup(UpdateCustomerGroup $command)
    {
        $customerGroup = $this->customerGroupRepository->findById($command->getId());

        if (null === $customerGroup) {
            return;
        }

        if ($command->getFallback()) {
            $this->removeFallback($command->getShopId(), $command->getId());
        }

        $customerGroup
            ->setName($command->getName())
            ->setInputGross($command->getInputGross())
            ->setShowGross($command->getShowGross())
            ->setShopId(new AptoUuid($command->getShopId()))
            ->setExternalId($command->getExternalId())
            ->setFallback($command->getFallback());

        $this->customerGroupRepository->update($customerGroup);
        $customerGroup->publishEvents();
    }

    /**
     * @param RemoveCustomerGroup $command
     */
    public function handleRemoveCustomerGroup(RemoveCustomerGroup $command)
    {
        $customerGroup = $this->customerGroupRepository->findById($command->getId());

        if (null === $customerGroup) {
            return;
        }

        $this->customerGroupRepository->remove($customerGroup);
        DomainEventPublisher::instance()->publish(
            new CustomerGroupRemoved(
                $customerGroup->getId()
            )
        );
    }

    /**
     * @param string $shopId
     * @param string|null $exclude
     */
    private function removeFallback(string $shopId, string $exclude = null)
    {
        if (null === $exclude) {
            $fallbackToRemove = $this->customerGroupRepository->findFallback($shopId);
        } else {
            $fallbackToRemove = $this->customerGroupRepository->findFallbackWithExcludeId($shopId, $exclude);
        }

        /** @var CustomerGroup $fallback */
        foreach ($fallbackToRemove as $fallback) {
            $fallback->setFallback(false);
            $this->customerGroupRepository->update($fallback);
            $fallback->publishEvents();
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddCustomerGroup::class => [
            'method' => 'handleAddCustomerGroup',
            'bus' => 'command_bus'
        ];

        yield UpdateCustomerGroup::class => [
            'method' => 'handleUpdateCustomerGroup',
            'bus' => 'command_bus'
        ];

        yield RemoveCustomerGroup::class => [
            'method' => 'handleRemoveCustomerGroup',
            'bus' => 'command_bus'
        ];
    }
}