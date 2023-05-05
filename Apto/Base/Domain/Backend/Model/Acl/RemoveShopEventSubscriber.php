<?php

namespace Apto\Base\Domain\Backend\Model\Acl;

use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventSubscriber;
use Apto\Base\Domain\Core\Model\Shop\ShopRemoved;

class RemoveShopEventSubscriber implements DomainEventSubscriber
{

    /**
     * @var AclEntryRepository
     */
    protected $aclEntryRepository;

    /**
     * RemoveShopEventSubscriber constructor.
     * @param AclEntryRepository $aclEntryRepository
     */
    public function __construct(AclEntryRepository $aclEntryRepository)
    {
        $this->aclEntryRepository = $aclEntryRepository;
    }

    /**
     * @param DomainEvent $event
     */
    public function handle(DomainEvent $event)
    {
        /** @var ShopRemoved $event */
        $shopId = $event->getId()->getId();
        $this->aclEntryRepository->removeByShopId($shopId);
    }

    /**
     * @param DomainEvent $event
     * @return bool
     */
    public function isSubscribedTo(DomainEvent $event)
    {
        return ($event instanceof ShopRemoved);
    }
}