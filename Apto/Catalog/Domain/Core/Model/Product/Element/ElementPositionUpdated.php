<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementPositionUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $sectionId;

    /**
     * @var AptoUuid
     */
    private $elementId;

    /**
     * @var int
     */
    private $position;

    /**
     * ElementPositionUpdated constructor.
     * @param AptoUuid $id
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param int $position
     */
    public function __construct(AptoUuid $id, AptoUuid $sectionId, AptoUuid $elementId, int $position)
    {
        parent::__construct($id);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->position = $position;
    }

    /**
     * @return AptoUuid
     */
    public function getSectionId(): AptoUuid
    {
        return $this->sectionId;
    }

    /**
     * @return AptoUuid
     */
    public function getElementId(): AptoUuid
    {
        return $this->elementId;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}