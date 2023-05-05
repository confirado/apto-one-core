<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class GroupAllowMultipleChanged extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $allowMultiple;

    /**
     * GroupAllowMultipleChanged constructor.
     * @param AptoUuid $id
     * @param bool $allowMultiple
     */
    public function __construct(AptoUuid $id, bool $allowMultiple)
    {
        parent::__construct($id);
        $this->allowMultiple = $allowMultiple;
    }

    /**
     * @return bool
     */
    public function getAllowMultiple(): bool
    {
        return $this->allowMultiple;
    }
}