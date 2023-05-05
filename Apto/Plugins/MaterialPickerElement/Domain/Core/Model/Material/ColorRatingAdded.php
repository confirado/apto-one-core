<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Color;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ColorRatingAdded extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $colorRatingId;

    /**
     * @var Color
     */
    private $color;

    /**
     * @var int
     */
    private $rating;

    /**
     * ColorRatingAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $colorRatingId
     * @param Color $color
     * @param int $rating
     */
    public function __construct(AptoUuid $id, AptoUuid $colorRatingId, Color $color, int $rating)
    {
        parent::__construct($id);
        $this->colorRatingId = $colorRatingId;
        $this->color = $color;
        $this->rating = $rating;
    }

    /**
     * @return AptoUuid
     */
    public function getColorRatingId(): AptoUuid
    {
        return $this->colorRatingId;
    }

    /**
     * @return Color
     */
    public function getColor(): Color
    {
        return $this->color;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }
}