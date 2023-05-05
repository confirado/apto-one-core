<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductMetaTitleUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue
     */
    private $metaTitle;

    /**
     * ProductMetaTitleUpdated constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $metaTitle
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $metaTitle)
    {
        parent::__construct($id);
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getMetaTitle(): AptoTranslatedValue
    {
        return $this->metaTitle;
    }
}