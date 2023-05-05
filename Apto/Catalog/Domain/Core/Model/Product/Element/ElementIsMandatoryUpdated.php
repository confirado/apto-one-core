<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementIsMandatoryUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $isMandatory;

    /**
     * ElementIsMandatoryUpdated constructor.
     * @param AptoUuid $id
     * @param bool $isMandatory
     */
    public function __construct(AptoUuid $id, bool $isMandatory)
    {
        parent::__construct($id);
        $this->isMandatory = $isMandatory;
    }

    /**
     * @return bool
     */
    public function getIsMandatory(): bool
    {
        return $this->isMandatory;
    }
}