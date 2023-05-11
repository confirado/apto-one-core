<?php

namespace Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class CanvasIdentifierChanged extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @param AptoUuid $id
     * @param string $identifier
     */
    public function __construct(AptoUuid $id, string $identifier)
    {
        parent::__construct($id);
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
