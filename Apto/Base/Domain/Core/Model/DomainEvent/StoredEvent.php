<?php

namespace Apto\Base\Domain\Core\Model\DomainEvent;

class StoredEvent implements DomainEvent
{
    /**
     * @var mixed
     * @phpstan-ignore-next-line
     */
    private $eventId;

    /**
     * @var string
     */
    private $eventBody;

    /**
     * @var \DateTimeImmutable|\DateTime
     */
    private $occurredOn;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @var string|null
     */
    private $userId;

    /**
     * StoredEvent constructor.
     * @param string $aTypeName
     * @param \DateTimeImmutable $anOccurredOn
     * @param string $anEventBody
     * @param string|null $userId
     */
    public function __construct(string $aTypeName, \DateTimeImmutable $anOccurredOn, string $anEventBody, $userId)
    {
        $this->eventBody = $anEventBody;
        $this->occurredOn = $anOccurredOn;
        $this->typeName = $aTypeName;
        $this->userId = $userId;
    }

    /**
     * cant use mixed here as return type because doctrine throws an error when return type dont match the actual mapping
     * @return mixed
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @return string
     */
    public function getEventBody(): string
    {
        return $this->eventBody;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn instanceof \DateTime ? \DateTimeImmutable::createFromMutable($this->occurredOn) : $this->occurredOn;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * @return string|null
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
