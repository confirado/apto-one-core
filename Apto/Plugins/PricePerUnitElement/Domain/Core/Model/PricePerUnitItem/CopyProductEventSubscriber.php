<?php

namespace Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem;

use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventSubscriber;
use Apto\Catalog\Domain\Core\Model\Product\ProductCopied;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Product\ProductElementCopied;
use Apto\Catalog\Domain\Core\Model\Product\ProductSectionCopied;

class CopyProductEventSubscriber implements DomainEventSubscriber
{

    /**
     * @var PricePerUnitItemRepository
     */
    protected $pricePerUnitItemRepository;

    /**
     * RemoveElementEventSubscriber constructor.
     * @param PricePerUnitItemRepository $pricePerUnitItemRepository
     */
    public function __construct(PricePerUnitItemRepository $pricePerUnitItemRepository)
    {
        $this->pricePerUnitItemRepository = $pricePerUnitItemRepository;
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

        // get related price per unit items
        $pricePerUnitItems = $this->pricePerUnitItemRepository->findByProductId($id);

        // copy all price per unit items
        $this->copyPricePerUnitItems($pricePerUnitItems, $entityMapping);
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

        // get related price per unit items
        $pricePerUnitItems = $this->pricePerUnitItemRepository->findBySectionId($id);

        // copy all price per unit items
        $this->copyPricePerUnitItems($pricePerUnitItems, $entityMapping);
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

        // get related price per unit items
        $pricePerUnitItem = $this->pricePerUnitItemRepository->findByElementId($id);
        if (null === $pricePerUnitItem) {
            return;
        }

        // copy price per unit item
        $this->copyPricePerUnitItem($pricePerUnitItem, $entityMapping);
    }

    /**
     * @param array $pricePerUnitItems
     * @param array $entityMapping
     * @throws InvalidUuidException
     */
    private function copyPricePerUnitItems(array $pricePerUnitItems, array $entityMapping)
    {
        // copy all price per unit items
        foreach ($pricePerUnitItems as $pricePerUnitItem) {
            $this->copyPricePerUnitItem($pricePerUnitItem, $entityMapping);
        }
    }

    /**
     * @param PricePerUnitItem $pricePerUnitItem
     * @param array $entityMapping
     * @throws InvalidUuidException
     */
    private function copyPricePerUnitItem(PricePerUnitItem $pricePerUnitItem, array $entityMapping)
    {
        // check mapping is valid
        if (
            !array_key_exists($pricePerUnitItem->getProductId()->getId(), $entityMapping) ||
            !array_key_exists($pricePerUnitItem->getSectionId()->getId(), $entityMapping) ||
            !array_key_exists($pricePerUnitItem->getElementId()->getId(), $entityMapping)
        ) {
            return;
        }

        // copy price per unit item
        $copiedPricePerUnitItem = $pricePerUnitItem->copy(
            $this->pricePerUnitItemRepository->nextIdentity(),
            $entityMapping[$pricePerUnitItem->getProductId()->getId()],
            $entityMapping[$pricePerUnitItem->getSectionId()->getId()],
            $entityMapping[$pricePerUnitItem->getElementId()->getId()]
        );

        // add new price per unit item
        $this->pricePerUnitItemRepository->add($copiedPricePerUnitItem);
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