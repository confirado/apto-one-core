<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\EventStore;
use Apto\Base\Domain\Core\Model\DomainEvent\StoredEvent;
use Apto\Base\Infrastructure\AptoBaseBundle\Security\User\AptoTokenUserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class EventStoreOrmRepository extends ServiceEntityRepository implements EventStore
{
    const ENTITY_CLASS = 'Apto\Base\Domain\Core\Model\DomainEvent\StoredEvent';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Security
     */
    private $security;

    /**
     * @param ManagerRegistry $registry
     * @param SerializerInterface $serializer
     * @param Security $security
     */
    public function __construct(ManagerRegistry $registry, SerializerInterface $serializer, Security $security)
    {
        parent::__construct($registry, static::ENTITY_CLASS);
        $this->serializer = $serializer;
        $this->security = $security;

    }

    /**
     * @param DomainEvent $aDomainEvent
     * @return void
     */
    public function append(DomainEvent $aDomainEvent)
    {
        $storedEvent = new StoredEvent(
            get_class($aDomainEvent),
            $aDomainEvent->getOccurredOn(),
            $this->serializer->serialize($aDomainEvent, 'json'),
            $this->getUserId()
        );
        $this->getEntityManager()->persist($storedEvent);
    }

    /**
     * @param $anEventId
     * @return array
     */
    public function allStoredEventsSince($anEventId)
    {
        $query = $this->createQueryBuilder('e');
        if ($anEventId) {
            $query->where('e.eventId > :eventId');
            $query->setParameters(['eventId' => $anEventId]);
        }
        $query->orderBy('e.eventId');
        return $query->getQuery()->getResult();
    }

    /**
     * @return string|null
     */
    private function getUserId()
    {
        if (null === $this->security->getUser()) {
            return null;
        }

        if ($this->security->getUser() instanceof AptoTokenUserInterface) {
            return $this->security->getUser()->getId();
        }
        return null;
    }
}
