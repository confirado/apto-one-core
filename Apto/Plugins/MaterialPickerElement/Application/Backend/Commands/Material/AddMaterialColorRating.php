<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class AddMaterialColorRating implements CommandInterface
{
    /**
     * @var string
     */
    private $materialId;

    /**
     * @var string
     */
    private $color;

    /**
     * @var int
     */
    private $rating;

    /**
     * AddMaterialColorRating constructor.
     * @param string $materialId
     * @param string $color
     * @param int $rating
     */
    public function __construct(string $materialId, string $color, int $rating) {
        $this->materialId = $materialId;
        $this->color = $color;
        $this->rating = $rating;
    }

    /**
     * @return string
     */
    public function getMaterialId(): string
    {
        return $this->materialId;
    }

    /**
     * @return string
     */
    public function getColor(): string
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