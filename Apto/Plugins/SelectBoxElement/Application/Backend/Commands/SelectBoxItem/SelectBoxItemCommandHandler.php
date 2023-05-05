<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItem;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItemRemoved;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItemRepository;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Money\Currency;
use Money\Money;

class SelectBoxItemCommandHandler extends AbstractCommandHandler
{
    /**
     * @var SelectBoxItemRepository
     */
    protected $selectBoxItemRepository;

    /**
     * SelectBoxItemCommandHandler constructor.
     * @param SelectBoxItemRepository $selectBoxItemRepository
     */
    public function __construct(SelectBoxItemRepository $selectBoxItemRepository)
    {
        $this->selectBoxItemRepository = $selectBoxItemRepository;
    }

    /**
     * @param AddSelectBoxItem $command
     * @throws InvalidUuidException
     */
    public function handleAddSelectBoxItem(AddSelectBoxItem $command)
    {
        $selectBoxItem = new SelectBoxItem(
            $this->selectBoxItemRepository->nextIdentity(),
            new AptoUuid($command->getProductId()),
            new AptoUuid($command->getSectionId()),
            new AptoUuid($command->getElementId()),
            AptoTranslatedValue::fromArray($command->getName())
        );

        $this->selectBoxItemRepository->add($selectBoxItem);
        $selectBoxItem->publishEvents();
    }

    /**
     * @param AddSelectBoxItems $command
     * @throws InvalidUuidException
     */
    public function handleAddSelectBoxItems(AddSelectBoxItems $command)
    {
        foreach ($command->getItems() as $item) {
            $selectBoxItem = new SelectBoxItem(
                $this->selectBoxItemRepository->nextIdentity(),
                new AptoUuid($command->getProductId()),
                new AptoUuid($command->getSectionId()),
                new AptoUuid($command->getElementId()),
                AptoTranslatedValue::fromArray($item)
            );

            $this->selectBoxItemRepository->add($selectBoxItem);
            $selectBoxItem->publishEvents();
        }
    }

    /**
     * @param UpdateSelectBoxItem $command
     * @throws InvalidUuidException
     */
    public function handleUpdateSelectBoxItem(UpdateSelectBoxItem $command)
    {
        $selectBoxItem = $this->selectBoxItemRepository->findById($command->getId());

        if (null !== $selectBoxItem) {
            $selectBoxItem
                ->setProductId(new AptoUuid($command->getProductId()))
                ->setSectionId(new AptoUuid($command->getSectionId()))
                ->setElementId(new AptoUuid($command->getElementId()))
                ->setName(AptoTranslatedValue::fromArray($command->getName()));

            $this->selectBoxItemRepository->update($selectBoxItem);
            $selectBoxItem->publishEvents();
        }
    }

    /**
     * @param RemoveSelectBoxItem $command
     */
    public function handleRemoveSelectBoxItem(RemoveSelectBoxItem $command)
    {
        $selectBoxItem = $this->selectBoxItemRepository->findById($command->getId());

        if (null !== $selectBoxItem) {
            $this->selectBoxItemRepository->remove($selectBoxItem);
            DomainEventPublisher::instance()->publish(
                new SelectBoxItemRemoved(
                    $selectBoxItem->getId()
                )
            );
        }
    }

    /**
     * @param RemoveSelectBoxItems $command
     */
    public function handleRemoveSelectBoxItems(RemoveSelectBoxItems $command)
    {
        foreach ($command->getIds() as $id) {
            $selectBoxItem = $this->selectBoxItemRepository->findById($id);

            if (null !== $selectBoxItem) {
                $this->selectBoxItemRepository->remove($selectBoxItem);
                DomainEventPublisher::instance()->publish(
                    new SelectBoxItemRemoved(
                        $selectBoxItem->getId()
                    )
                );
            }
        }
    }

    /**
     * @param AddSelectBoxItemPrice $command
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function handleAddSelectBoxItemPrice(AddSelectBoxItemPrice $command)
    {
        $selectBoxItem = $this->selectBoxItemRepository->findById($command->getSelectBoxItemId());

        if (null !== $selectBoxItem) {
            $selectBoxItem->addAptoPrice(
                new Money(
                    $command->getAmount(),
                    new Currency(
                        $command->getCurrency()
                    )
                ),
                new AptoUuid(
                    $command->getCustomerGroupId()
                )
            );
            $this->selectBoxItemRepository->update($selectBoxItem);
            $selectBoxItem->publishEvents();
        }
    }

    /**
     * @param RemoveSelectBoxItemPrice $command
     * @throws InvalidUuidException
     */
    public function handleRemoveSelectBoxItemPrice(RemoveSelectBoxItemPrice $command)
    {
        $selectBoxItem = $this->selectBoxItemRepository->findById($command->getSelectBoxItemId());

        if (null !== $selectBoxItem) {
            $selectBoxItem->removeAptoPrice(
                new AptoUuid(
                    $command->getPriceId()
                )
            );
            $this->selectBoxItemRepository->update($selectBoxItem);
            $selectBoxItem->publishEvents();
        }
    }

    public function handleSetSelectBoxItemIsDefault(SetSelectBoxItemIsDefault $command)
    {
        $selectBoxItem = $this->selectBoxItemRepository->findById($command->getSelectBoxItemId());

        if (null === $selectBoxItem) {
            return;
        }

        // if we want to remove default state its not important if another element is already set to default so we can just set to false
        if (false === $command->getIsDefault()) {
            $selectBoxItem->setIsDefault(false);
        } else {
            $items = $this->selectBoxItemRepository->findByElementId($command->getElementId());

            /** @var SelectBoxItem $item */
            foreach ($items as $item) {
                if (false === $item->getIsDefault()) {
                    continue;
                }

                $item->setIsDefault(false);
                $this->selectBoxItemRepository->update($item);
            }

            $selectBoxItem->setIsDefault(true);
        }

        $this->selectBoxItemRepository->update($selectBoxItem);
        $selectBoxItem->publishEvents();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddSelectBoxItem::class => [
            'method' => 'handleAddSelectBoxItem',
            'bus' => 'command_bus'
        ];

        yield AddSelectBoxItems::class => [
            'method' => 'handleAddSelectBoxItems',
            'bus' => 'command_bus'
        ];

        yield UpdateSelectBoxItem::class => [
            'method' => 'handleUpdateSelectBoxItem',
            'bus' => 'command_bus'
        ];

        yield RemoveSelectBoxItem::class => [
            'method' => 'handleRemoveSelectBoxItem',
            'bus' => 'command_bus'
        ];

        yield RemoveSelectBoxItems::class => [
            'method' => 'handleRemoveSelectBoxItems',
            'bus' => 'command_bus'
        ];

        yield AddSelectBoxItemPrice::class => [
            'method' => 'handleAddSelectBoxItemPrice',
            'bus' => 'command_bus'
        ];

        yield RemoveSelectBoxItemPrice::class => [
            'method' => 'handleRemoveSelectBoxItemPrice',
            'bus' => 'command_bus'
        ];

        yield SetSelectBoxItemIsDefault::class => [
            'method' => 'handleSetSelectBoxItemIsDefault',
            'bus' => 'command_bus'
        ];
    }
}