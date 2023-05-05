<?php

namespace Apto\Base\Domain\Core\Model;

interface AptoRepository
{
    /**
     * @return AptoUuid
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function nextIdentity(): AptoUuid;
}