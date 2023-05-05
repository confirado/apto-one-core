<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class RemoveMaterialColorRating implements CommandInterface
{
    /**
     * @var string
     */
    private $materialId;

    /**
     * @var string
     */
    private $colorRatingId;

    /**
     * RemoveMaterialColorRating constructor.
     * @param string $materialId
     * @param string $colorRatingId
     */
    public function __construct(string $materialId, string $colorRatingId) {
        $this->materialId = $materialId;
        $this->colorRatingId = $colorRatingId;
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
    public function getColorRatingId(): string
    {
        return $this->colorRatingId;
    }
}