<?php

namespace Apto\Base\Domain\Core\Model\DomainEvent;

interface DomainEvent
{
    /**
     * @return \DateTimeImmutable
     */
    public function getOccurredOn(): \DateTimeImmutable;
}