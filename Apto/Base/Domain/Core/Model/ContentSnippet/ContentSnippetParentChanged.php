<?php

namespace Apto\Base\Domain\Core\Model\ContentSnippet;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ContentSnippetParentChanged extends AbstractDomainEvent
{
    /**
     * @var ContentSnippet|null
     */
    private $parent;

    /**
     * ContentSnippetParentChanged constructor.
     * @param AptoUuid $id
     * @param ContentSnippet|null $parent
     */
    public function __construct(AptoUuid $id, ?ContentSnippet $parent)
    {
        parent::__construct($id);
        $this->parent = $parent;
    }

    /**
     * @return ContentSnippet|null
     */
    public function getParent(): ?ContentSnippet
    {
        return $this->parent;
    }
}