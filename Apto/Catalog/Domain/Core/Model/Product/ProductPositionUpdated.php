<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductPositionUpdated extends AbstractDomainEvent
{
    /**
     * @var int
     */
    private $position;

    /**
     * ProductPositionUpdated constructor.
     * @param AptoUuid $id
     * @param int $position
     */
    public function __construct(AptoUuid $id, int $position)
    {
        parent::__construct($id);
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}