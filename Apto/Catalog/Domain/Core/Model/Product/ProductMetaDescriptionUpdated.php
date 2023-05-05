<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductMetaDescriptionUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue
     */
    private $metaDescription;

    /**
     * ProductMetaDescriptionUpdated constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $metaDescription
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $metaDescription)
    {
        parent::__construct($id);
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getMetaDescription(): AptoTranslatedValue
    {
        return $this->metaDescription;
    }
}