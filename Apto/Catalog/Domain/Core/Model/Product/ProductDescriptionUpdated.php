<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductDescriptionUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue
     */
    private $description;

    /**
     * ProductDescriptionUpdated constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $description
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $description)
    {
        parent::__construct($id);
        $this->description = $description;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getDescription(): AptoTranslatedValue
    {
        return $this->description;
    }
}