<?php

namespace Apto\Base\Domain\Core\Model\AptoPrice;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

abstract class AbstractAptoPriceEvent extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    protected $reference;

    /**
     * AbstractAptoPriceEvent constructor.
     * @param AptoUuid $id
     * @param AptoUuid $reference
     */
    public function __construct(AptoUuid $id, AptoUuid $reference)
    {
        parent::__construct($id);
        $this->reference = $reference;
    }

    /**
     * @return AptoUuid
     */
    public function getReference(): AptoUuid
    {
        return $this->reference;
    }
}