<?php

namespace Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem;

use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventSubscriber;
use Apto\Catalog\Domain\Core\Model\Product\ProductElementRemoved;
use Apto\Catalog\Domain\Core\Model\Product\ProductRemoved;
use Apto\Catalog\Domain\Core\Model\Product\ProductSectionRemoved;

class RemoveElementEventSubscriber implements DomainEventSubscriber
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
     */
    public function handle(DomainEvent $event)
    {
        /** @var AbstractDomainEvent $event */
        $id = $event->getId()->getId();

        // get related pricePerUnitItems
        $pricePerUnitItems = [];
        if ($event instanceof ProductRemoved) {
            $pricePerUnitItems = $this->pricePerUnitItemRepository->findByProductId($id);
        }
        if ($event instanceof ProductSectionRemoved) {
            $pricePerUnitItems = $this->pricePerUnitItemRepository->findBySectionId($id);
        }
        if ($event instanceof ProductElementRemoved) {
            $item = $this->pricePerUnitItemRepository->findByElementId($id);
            if (null !== $item) {
                $pricePerUnitItems = [$item];
            }
        }

        // delete all pricePerUnitItems
        foreach ($pricePerUnitItems as $pricePerUnitItem) {
            $this->pricePerUnitItemRepository->remove($pricePerUnitItem);
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