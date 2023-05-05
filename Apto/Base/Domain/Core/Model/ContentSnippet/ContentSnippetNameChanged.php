<?php

namespace Apto\Base\Domain\Core\Model\ContentSnippet;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ContentSnippetNameChanged extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * CustomerGroupNameChanged constructor.
     * @param AptoUuid $id
     * @param string $name
     */
    public function __construct(AptoUuid $id, string $name)
    {
        parent::__construct($id);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}