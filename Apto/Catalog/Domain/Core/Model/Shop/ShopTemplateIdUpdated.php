<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ShopTemplateIdUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $templateId;

    /**
     * ShopTemplateIdUpdated constructor.
     * @param AptoUuid $id
     * @param string $templateId
     */
    public function __construct(AptoUuid $id, $templateId)
    {
        parent::__construct($id);
        $this->templateId = $templateId;
    }

    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }
}