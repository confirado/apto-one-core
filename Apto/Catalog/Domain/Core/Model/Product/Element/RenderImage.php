<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Doctrine\Common\Collections\Collection;
use http\Exception\InvalidArgumentException;

class RenderImage extends AptoEntity
{
    /* valid offset units */
    const OFFSET_UNIT_PERCENT = 0;
    const OFFSET_UNIT_PIXEL = 1;
    const VALID_OFFSET_UNITS = [
        self::OFFSET_UNIT_PERCENT,
        self::OFFSET_UNIT_PIXEL
    ];

    /**
     * @var int
     */
    protected $layer;

    /**
     * @var string
     */
    protected $perspective;

    /**
     * @var float
     */
    protected $offsetX;

    /**
     * @var int
     */
    protected $offsetUnitX;

    /**
     * @var float
     */
    protected $offsetY;

    /**
     * @var int
     */
    protected $offsetUnitY;

    /**
     * @var MediaFile
     */
    protected $mediaFile;

    /**
     * @var Element
     */
    protected $element;

    /**
     * @var RenderImageOptions
     */
    protected $renderImageOptions;

    /**
     * RenderImage constructor.
     * @param AptoUuid $id
     * @param int $layer
     * @param string $perspective
     * @param MediaFile $mediaFile
     * @param Element $element
     * @param float $offsetX
     * @param int $offsetUnitX
     * @param float $offsetY
     * @param int $offsetUnitY
     * @param RenderImageOptions $renderImageOptions
     */
    public function __construct(AptoUuid $id, int $layer, string $perspective, MediaFile $mediaFile, Element $element, float $offsetX, int $offsetUnitX, float $offsetY, int $offsetUnitY, RenderImageOptions $renderImageOptions)
    {
        self::assertValidOffsetUnit($offsetUnitX);
        self::assertValidOffsetUnit($offsetUnitY);

        parent::__construct($id);
        $this->layer = $layer;
        $this->perspective = $perspective;
        $this->mediaFile = $mediaFile;
        $this->element = $element;
        $this->offsetX = $offsetX;
        $this->offsetUnitX = $offsetUnitX;
        $this->offsetY = $offsetY;
        $this->offsetUnitY = $offsetUnitY;
        $this->renderImageOptions = $renderImageOptions;

    }

    /**
     * @param int $unit
     * @return void
     */
    protected static function assertValidOffsetUnit(int $unit)
    {
        if (!in_array($unit, self::VALID_OFFSET_UNITS)) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" is not within the valid offset unit types (%s).',
                $unit,
                implode(', ', self::VALID_OFFSET_UNITS)
            ));
        }
    }

    /**
     * @return int
     */
    public function getLayer(): int
    {
        return $this->layer;
    }

    /**
     * @return float
     */
    public function getOffsetX(): float
    {
        if ($this->offsetX === null) {
            return 0;
        }
        return $this->offsetX;
    }

    /**
     * @return int
     */
    public function getOffsetUnitX(): int
    {
        return $this->offsetUnitX;
    }

    /**
     * @return float
     */
    public function getOffsetY(): float
    {
        if ($this->offsetY === null) {
            return 0;
        }
        return $this->offsetY;
    }

    /**
     * @return int
     */
    public function getOffsetUnitY(): int
    {
        return $this->offsetUnitY;
    }

    /**
     * @return string
     */
    public function getPerspective(): string
    {
        return $this->perspective;
    }

    /**
     * @return MediaFile
     */
    public function getMediaFile(): MediaFile
    {
        return $this->mediaFile;
    }

    /**
     * @return Element
     */
    public function getElement(): Element
    {
        return $this->element;
    }

    /**
     * @return RenderImageOptions
     */
    public function getRenderImageOptions(): RenderImageOptions
    {
        // backwards compatibility for old render images
        if (!$this->renderImageOptions) {
            return RenderImageOptions::fromArray([
                'renderImageOptions' => [
                    'name' => '',
                    'file' => $this->getMediaFile()->getFile()->getPath(),
                    'perspective' => $this->getPerspective(),
                    'layer' => $this->getLayer(),
                    'type' => 'Statisch',
                    'formulaHorizontal' => '',
                    'formulaVertical' => '',
                    'elementValueRefs' => []
                ],
                'offsetOptions' => [
                    'offsetX' => $this->getOffsetX(),
                    'offsetUnitX' => $this->getOffsetUnitX(),
                    'offsetY' => $this->getOffsetY(),
                    'offsetUnitY' => $this->getOffsetUnitY(),
                    'type' => 'Statisch',
                    'formulaOffsetX' => '',
                    'formulaOffsetY' => '',
                    'elementValueRefs' => []
                ]
            ]);
        }

        return $this->renderImageOptions;
    }

    /**
     * @param AptoUuid $id
     * @param Collection $entityMapping
     * @return RenderImage
     */
    public function copy(AptoUuid $id, Collection &$entityMapping): RenderImage
    {
        // create new renderImage
        $renderImage = new RenderImage(
            $id,
            $this->getLayer(),
            $this->getPerspective(),
            $this->getMediaFile(),
            $entityMapping->get($this->element->getId()->getId()),
            $this->getOffsetX(),
            $this->getOffsetUnitX(),
            $this->getOffsetY(),
            $this->getOffsetUnitY(),
            $this->getRenderImageOptions()
        );

        // return new renderImage
        return $renderImage;
    }
}