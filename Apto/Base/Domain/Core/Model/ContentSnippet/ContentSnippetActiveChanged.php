<?php

namespace Apto\Base\Domain\Core\Model\ContentSnippet;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ContentSnippetActiveChanged extends AbstractDomainEvent
{

    /**
     * @var bool
     */
    private $active;

    /**
     * ContentSnippedActiveChanged constructor.
     * @param AptoUuid $id
     * @param bool $active
     */
    public function __construct(AptoUuid $id, bool $active)
    {
        parent::__construct($id);
        $this->active= $active;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }
}