<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementZoomFunctionUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $zoomFunction;

    /**
     * @param AptoUuid $id
     * @param string $zoomFunction
     */
    public function __construct(AptoUuid $id, string $zoomFunction)
    {
        parent::__construct($id);
        $this->zoomFunction = $zoomFunction;
    }

    /**
     * @return string
     */
    public function getZoomFunction(): string
    {
        return $this->zoomFunction;
    }
}