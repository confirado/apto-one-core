<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductNameUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * ProductNameUpdated constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->name = $name;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }
}