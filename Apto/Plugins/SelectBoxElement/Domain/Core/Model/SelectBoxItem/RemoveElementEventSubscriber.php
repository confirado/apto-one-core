<?php

namespace Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem;

use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventSubscriber;
use Apto\Catalog\Domain\Core\Model\Product\ProductElementRemoved;
use Apto\Catalog\Domain\Core\Model\Product\ProductRemoved;
use Apto\Catalog\Domain\Core\Model\Product\ProductSectionRemoved;

class RemoveElementEventSubscriber implements DomainEventSubscriber
{

    /**
     * @var SelectBoxItemRepository
     */
    protected $selectBoxItemRepository;

    /**
     * RemoveElementEventSubscriber constructor.
     * @param SelectBoxItemRepository $selectBoxItemRepository
     */
    public function __construct(SelectBoxItemRepository $selectBoxItemRepository)
    {
        $this->selectBoxItemRepository = $selectBoxItemRepository;
    }

    /**
     * @param DomainEvent $event
     */
    public function handle(DomainEvent $event)
    {
        /** @var AbstractDomainEvent $event */
        $id = $event->getId()->getId();

        // get related selectBoxItems
        $selectBoxItems = [];
        if ($event instanceof ProductRemoved) {
            $selectBoxItems = $this->selectBoxItemRepository->findByProductId($id);
        }
        if ($event instanceof ProductSectionRemoved) {
            $selectBoxItems = $this->selectBoxItemRepository->findBySectionId($id);
        }
        if ($event instanceof ProductElementRemoved) {
            $selectBoxItems = $this->selectBoxItemRepository->findByElementId($id);
        }

        // delete all selectBoxItems
        foreach ($selectBoxItems as $selectBoxItem) {
            $this->selectBoxItemRepository->remove($selectBoxItem);
        }
    }

    /**
     * @param DomainEvent $event
     * @return bool
     */
    public function isSubscribedTo(DomainEvent $event)
    {
        return (
            ($event instanceof ProductRemoved) ||
            ($event instanceof ProductSectionRemoved) ||
            ($event instanceof ProductElementRemoved)
        );
    }
}