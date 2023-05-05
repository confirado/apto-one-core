<?php

namespace Apto\Base\Domain\Core\Model\DomainEvent;

use Apto\Base\Domain\Core\Model\AptoUuid;

abstract class AbstractDomainEvent implements DomainEvent
{
    /**
     * @var AptoUuid
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     */
    private $occurredOn;

    /**
     * ShopPersisted constructor.
     * @param AptoUuid $id
     */
    public function __construct(AptoUuid $id)
    {
        $this->id = $id;
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * @return AptoUuid
     */
    public function getId(): AptoUuid
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}