<?php

namespace Apto\Base\Domain\Core\Model\AptoPriceFormula;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

abstract class AbstractAptoPriceFormulaEvent extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    protected $reference;

    /**
     * AbstractAptoPriceFormulaEvent constructor.
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