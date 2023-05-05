<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialReflectionChanged extends AbstractDomainEvent
{
    /**
     * @var int|null
     */
    private $reflection;

    /**
     * MaterialReflectionChanged constructor.
     * @param AptoUuid $id
     * @param int|null $reflection
     */
    public function __construct(AptoUuid $id, $reflection)
    {
        parent::__construct($id);
        $this->reflection = $reflection;
    }

    /**
     * @return int|null
     */
    public function getReflection()
    {
        return $this->reflection;
    }
}