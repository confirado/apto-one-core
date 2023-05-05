<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductFilterPropertiesUpdated extends AbstractDomainEvent
{
    /**
     * @var array
     */
    private $filterProperties;

    /**
     * CategoryNameUpdated constructor.
     * @param AptoUuid $id
     * @param array $filterProperties
     */
    public function __construct(AptoUuid $id, array $filterProperties)
    {
        parent::__construct($id);
        $this->filterProperties = $filterProperties;
    }

    /**
     * @return array
     */
    public function getFilterProperties()
    {
        return $this->filterProperties;
    }
}