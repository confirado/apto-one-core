<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ColorRatingRemoved extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $colorRatingId;

    /**
     * ColorRatingAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $colorRatingId
     */
    public function __construct(AptoUuid $id, AptoUuid $colorRatingId)
    {
        parent::__construct($id);
        $this->colorRatingId = $colorRatingId;
    }

    /**
     * @return AptoUuid
     */
    public function getColorRatingId(): AptoUuid
    {
        return $this->colorRatingId;
    }
}