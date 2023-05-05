<?php

namespace Apto\Base\Domain\Core\Model\ContentSnippet;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ContentSnippetContentChanged extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue
     */
    private $content;

    /**
     * ContentSnippetContentChanged constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $content
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $content)
    {
        parent::__construct($id);
        $this->content = $content;
    }

    /**
     * @return bool
     */
    public function getContent(): AptoTranslatedValue
    {
        return $this->content;
    }
}