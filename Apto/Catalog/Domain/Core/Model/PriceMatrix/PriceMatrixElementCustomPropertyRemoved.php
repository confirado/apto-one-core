<?php

namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceMatrixElementCustomPropertyRemoved extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $customPropertyId;

    /**
     * @param AptoUuid $id
     * @param string $customPropertyId
     */
    public function __construct(AptoUuid $id, string $customPropertyId)
    {
        parent::__construct($id);
        $this->customPropertyId = $customPropertyId;
    }

    /**
     * @return string
     */
    public function getCustomPropertyId(): string
    {
        return $this->customPropertyId;
    }
}
