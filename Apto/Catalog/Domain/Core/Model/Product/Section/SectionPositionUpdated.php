<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Section;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class SectionPositionUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $sectionId;

    /**
     * @var int
     */
    private $position;

    /**
     * SectionPositionUpdated constructor.
     * @param AptoUuid $id
     * @param AptoUuid $sectionId
     * @param int $position
     */
    public function __construct(AptoUuid $id, AptoUuid $sectionId, int $position)
    {
        parent::__construct($id);
        $this->sectionId = $sectionId;
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
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}