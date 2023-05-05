<?php

namespace Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem;

use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventSubscriber;
use Apto\Catalog\Domain\Core\Model\Product\ProductCopied;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Product\ProductElementCopied;
use Apto\Catalog\Domain\Core\Model\Product\ProductSectionCopied;

class CopyProductEventSubscriber implements DomainEventSubscriber
{

    /**
     * @var FloatInputItemRepository
     */
    protected $floatInputItemRepository;

    /**
     * RemoveElementEventSubscriber constructor.
     * @param FloatInputItemRepository $floatInputItemRepository
     */
    public function __construct(FloatInputItemRepository $floatInputItemRepository)
    {
        $this->floatInputItemRepository = $floatInputItemRepository;
    }

    /**
     * @param DomainEvent $event
     * @throws InvalidUuidException
     */
    public function handle(DomainEvent $event)
    {
        if ($event instanceof ProductCopied) {
            $this->handleProductCopied($event);
        }

        if ($event instanceof ProductSectionCopied) {
            $this->handleProductSectionCopied($event);
        }

        if ($event instanceof ProductElementCopied) {
            $this->handleProductElementCopied($event);
        }
    }

    /**
     * @param DomainEvent $event
     * @throws InvalidUuidException
     */
    private function handleProductCopied(DomainEvent $event)
    {
        /** @var ProductCopied $event */
        $id = $event->getProductId()->getId();
        $entityMapping = $event->getEntityMapping();

        // get related float input items
        $floatInputItems = $this->floatInputItemRepository->findByProductId($id);

        // copy all float input items
        $this->copyFloatInputItems($floatInputItems, $entityMapping);
    }

    /**
     * @param DomainEvent $event
     * @throws InvalidUuidException
     */
    private function handleProductSectionCopied(DomainEvent $event)
    {
        /** @var ProductSectionCopied $event */
        $id = $event->getSectionId()->getId();
        $entityMapping = $event->getEntityMapping();

        // get related float input items
        $floatInputItems = $this->floatInputItemRepository->findBySectionId($id);

        // copy all float input items
        $this->copyFloatInputItems($floatInputItems, $entityMapping);
    }

    /**
     * @param DomainEvent $event
     * @throws InvalidUuidException
     */
    private function handleProductElementCopied(DomainEvent $event)
    {
        /** @var ProductElementCopied $event */
        $id = $event->getElementId()->getId();
        $entityMapping = $event->getEntityMapping();

        // get related float input items
        $floatInputItem = $this->floatInputItemRepository->findByElementId($id);
        if (null === $floatInputItem) {
            return;
        }

        // copy float input item
        $this->copyFloatInputItem($floatInputItem, $entityMapping);
    }

    /**
     * @param array $floatInputItems
     * @param array $entityMapping
     * @throws InvalidUuidException
     */
    private function copyFloatInputItems(array $floatInputItems, array $entityMapping)
    {
        // copy all float input items
        foreach ($floatInputItems as $floatInputItem) {
            $this->copyFloatInputItem($floatInputItem, $entityMapping);
        }
    }

    /**
     * @param FloatInputItem $floatInputItem
     * @param array $entityMapping
     * @throws InvalidUuidException
     */
    private function copyFloatInputItem(FloatInputItem $floatInputItem, array $entityMapping)
    {
        /** @var FloatInputItem $floatInputItem */
        // check mapping is valid
        if (
            !array_key_exists($floatInputItem->getProductId()->getId(), $entityMapping) ||
            !array_key_exists($floatInputItem->getSectionId()->getId(), $entityMapping) ||
            !array_key_exists($floatInputItem->getElementId()->getId(), $entityMapping)
        ) {
            return;
        }

        // copy float input item
        $copiedFloatInputItem = $floatInputItem->copy(
            $this->floatInputItemRepository->nextIdentity(),
            $entityMapping[$floatInputItem->getProductId()->getId()],
            $entityMapping[$floatInputItem->getSectionId()->getId()],
            $entityMapping[$floatInputItem->getElementId()->getId()]
        );

        // add new float input item
        $this->floatInputItemRepository->add($copiedFloatInputItem);
    }

    /**
     * @param DomainEvent $event
     * @return bool
     */
    public function isSubscribedTo(DomainEvent $event)
    {
        return (
            $event instanceof ProductCopied ||
            $event instanceof ProductSectionCopied ||
            $event instanceof ProductElementCopied
        );
    }
}