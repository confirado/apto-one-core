<?php

namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceMatrixElementCustomPropertyRemoved extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param AptoUuid $id
     * @param string $key
     */
    public function __construct(AptoUuid $id, string $key)
    {
        parent::__construct($id);
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
