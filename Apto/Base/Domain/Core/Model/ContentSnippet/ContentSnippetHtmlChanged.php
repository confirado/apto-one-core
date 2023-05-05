<?php

namespace Apto\Base\Domain\Core\Model\ContentSnippet;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ContentSnippetHtmlChanged extends AbstractDomainEvent
{

    /**
     * @var bool
     */
    private $html;

    /**
     * ContentSnippedHtmlChanged constructor.
     * @param AptoUuid $id
     * @param bool $html
     */
    public function __construct(AptoUuid $id, bool $html)
    {
        parent::__construct($id);
        $this->html= $html;
    }

    /**
     * @return bool
     */
    public function getHtml(): bool
    {
        return $this->html;
    }
}