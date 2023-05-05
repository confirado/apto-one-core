<?php

namespace Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem;

use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventSubscriber;
use Apto\Catalog\Domain\Core\Model\Product\ProductElementRemoved;
use Apto\Catalog\Domain\Core\Model\Product\ProductRemoved;
use Apto\Catalog\Domain\Core\Model\Product\ProductSectionRemoved;

class RemoveElementEventSubscriber implements DomainEventSubscriber
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
     */
    public function handle(DomainEvent $event)
    {
        /** @var AbstractDomainEvent $event */
        $id = $event->getId()->getId();

        // get related floatInputItems
        $floatInputItems = [];
        if ($event instanceof ProductRemoved) {
            $floatInputItems = $this->floatInputItemRepository->findByProductId($id);
        }
        if ($event instanceof ProductSectionRemoved) {
            $floatInputItems = $this->floatInputItemRepository->findBySectionId($id);
        }
        if ($event instanceof ProductElementRemoved) {
            $item = $this->floatInputItemRepository->findByElementId($id);
            if (null !== $item) {
                $floatInputItems = [$item];
            }
        }

        // delete all floatInputItems
        foreach ($floatInputItems as $floatInputItem) {
            $this->floatInputItemRepository->remove($floatInputItem);
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