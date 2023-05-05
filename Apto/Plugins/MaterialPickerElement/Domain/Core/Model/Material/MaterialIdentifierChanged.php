<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialIdentifierChanged extends AbstractDomainEvent
{
    /**
     * @var string|null
     */
    private $identifier;

    /**
     * MaterialIdentifierChanged constructor.
     * @param AptoUuid $id
     * @param string|null $identifier
     */
    public function __construct(AptoUuid $id, $identifier)
    {
        parent::__construct($id);
        $this->identifier = $identifier;
    }

    /**
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}