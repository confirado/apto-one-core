<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Color;

class ColorRating extends AptoEntity
{
    /**
     * @var Material
     */
    private $material;

    /**
     * @var Color
     */
    private $color;

    /**
     * @var int
     */
    private $rating;

    /**
     * ColorRating constructor.
     * @param AptoUuid $id
     * @param Material $material
     * @param Color $color
     * @param int $rating
     */
    public function __construct(AptoUuid $id, Material $material, Color $color, int $rating)
    {
        parent::__construct($id);
        $this->material = $material;
        $this->color = $color;
        $this->rating = $rating;
    }

    /**
     * @return Material
     */
    public function getMaterial(): Material
    {
        return $this->material;
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
