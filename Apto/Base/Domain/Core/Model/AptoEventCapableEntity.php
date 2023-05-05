<?php

namespace Apto\Base\Domain\Core\Model;

use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Doctrine\Common\Collections\Collection;

abstract class AptoEventCapableEntity
{
    /**
     * @var AptoUuid
     */
    protected $id;

    /**
     * @var mixed
     */
    protected $surrogateId;

    /**
     * @var \DateTimeImmutable
     */
    protected $created;

    /**
     * @var array
     */
    protected $eventsToPublish = [];

    /**
     * AptoEntity constructor.
     * @param AptoUuid $id
     */
    public function __construct(AptoUuid $id)
    {
        $this->id = $id;
        do {
            $this->created = \DateTimeImmutable::createFromFormat('U.u', microtime(true));
        } while($this->created === false);
    }

    /**
     * @return AptoUuid
     */
    public function getId(): AptoUuid
    {
        return $this->id;
    }

    /**
     * cant use mixed here as return type because doctrine throws an error when return type dont match the actual mapping
     * @return mixed
     */
    public function getSurrogateId()
    {
        return $this->surrogateId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreated(): \DateTimeImmutable
    {
        return $this->created instanceof \DateTime ? \DateTimeImmutable::createFromMutable($this->created) : $this->created;
    }

    /**
     * @param DomainEvent $domainEvent
     */
    protected function publish(DomainEvent $domainEvent)
    {
        $this->eventsToPublish[] = $domainEvent;
    }

    /**
     * bubble/queue events from given AptoEntity
     * @param AptoEntity $entity
     */
    protected function bubbleEvents(AptoEntity $entity)
    {
        $this->eventsToPublish = array_merge($this->eventsToPublish, $entity->getAndClearPublishedEvents());
    }

    /**
     * @param Collection $collection
     * @return array
     */
    protected function getCollectionIds(Collection $collection): array
    {
        $collectionIds = [];
        foreach ($collection as $item) {
            if ($item instanceof self) {
                $collectionIds[] = $item->getId();
            }
        }
        return $collectionIds;
    }

    /**
     * @param Collection $oldCollection
     * @param Collection $newCollection
     * @return bool
     */
    protected function hasCollectionChanged(Collection $oldCollection, Collection $newCollection): bool
    {
        if ($oldCollection->count() !== $newCollection->count()) {
            return true;
        }

        $collectionHasChanged = false;
        foreach ($newCollection as $newCollectionItem) {
            if (!$oldCollection->contains($newCollectionItem)) {
                $collectionHasChanged = true;
                break;
            }
        }
        return $collectionHasChanged;
    }
}