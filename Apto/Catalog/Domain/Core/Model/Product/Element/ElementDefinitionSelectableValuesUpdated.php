<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementDefinitionSelectableValuesUpdated extends AbstractDomainEvent
{

    /**
     * @var string
     */
    private $oldValues;

    /**
     * @var string
     */
    private $newValues;

    /**
     * ElementDefinitionSelectableValuesUpdated constructor.
     * @param AptoUuid $id
     * @param string $oldValues
     * @param string $newValues
     */
    public function __construct(AptoUuid $id, string $oldValues, string $newValues)
    {
        parent::__construct($id);
        $this->oldValues = $oldValues;
        $this->newValues = $newValues;
    }

    /**
     * @return string
     */
    public function getOldValues(): string
    {
        return $this->oldValues;
    }

    /**
     * @return string
     */
    public function getNewValues(): string
    {
        return $this->newValues;
    }


}