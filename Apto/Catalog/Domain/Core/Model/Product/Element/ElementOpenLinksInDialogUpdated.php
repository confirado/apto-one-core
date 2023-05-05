<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementOpenLinksInDialogUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $openLinksInDialog;

    /**
     * @param AptoUuid $id
     * @param bool $openLinksInDialog
     */
    public function __construct(AptoUuid $id, bool $openLinksInDialog)
    {
        parent::__construct($id);
        $this->openLinksInDialog = $openLinksInDialog;
    }

    /**
     * @return bool
     */
    public function getOpenLinksInDialog(): bool
    {
        return $this->openLinksInDialog;
    }
}