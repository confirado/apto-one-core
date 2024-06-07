<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPrices;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Color;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\Pool;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\Property;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Material extends AptoAggregate
{
    use AptoPrices;

    /**
     * @phpstan-ignore-next-line
     * @var Collection
     */
    private $poolItems;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var string|null
     */
    private $identifier;

    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * @var AptoTranslatedValue
     */
    private $description;

    /**
     * @var int
     */
    private $clicks;

    /**
     * @var MediaFile|null
     */
    private $previewImage;

    /**
     * @var Collection
     */
    private $galleryImages;

    /**
     * @var Collection
     */
    private $properties;

    /**
     * @var Collection
     */
    private $colorRatings;

    /**
     * @var Collection
     */
    protected $renderImages;

    /**
     * @var int|null
     */
    private $reflection;

    /**
     * @var int|null
     */
    private $transmission;

    /**
     * @var int|null
     */
    private $absorption;

    /**
     * @var bool
     */
    private $isNotAvailable;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array|null
     */
    protected $conditionSets;

    /**
     * Material constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->poolItems = new ArrayCollection();
        $this->galleryImages = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->colorRatings = new ArrayCollection();
        $this->renderImages = new ArrayCollection();

        $this->name = $name;
        $this->description = new AptoTranslatedValue([]);

        $this->active = true;
        $this->isNotAvailable = false;
        $this->identifier = null;
        $this->clicks = 0;
        $this->previewImage = null;
        $this->reflection = null;
        $this->transmission = null;
        $this->absorption = null;
        $this->position = 0;
        $this->conditionSets = [];

        $this->publish(
            new MaterialAdded(
                $this->getId(),
                $name
            )
        );
    }

    /**
     * @param AptoUuid $conditionSetId
     *
     * @return $this
     */
    public function addConditionSet(AptoUuid $conditionSetId): Material
    {
        if (is_null($this->conditionSets)) {
            $this->conditionSets[] = [];
        }

        if (!in_array($conditionSetId->getId(), $this->conditionSets)) {
            $this->conditionSets[] = $conditionSetId->getId();
        }

        return $this;
    }

    /**
     * @param AptoUuid $conditionSetId
     *
     * @return $this
     */
    public function removeConditionSet(AptoUuid $conditionSetId): Material
    {
        if (is_null($this->conditionSets)) {
            $this->conditionSets[] = [];
        }

        if (in_array($conditionSetId->getId(), $this->conditionSets)) {
            $key = array_search($conditionSetId->getId(), $this->conditionSets);
            unset($this->conditionSets[$key]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Material
     */
    public function setActive(bool $active): Material
    {
        if ($this->active === $active) {
            return $this;
        }
        $this->active = $active;
        $this->publish(
            new MaterialActiveChanged(
                $this->getId(),
                $this->getActive()
            )
        );
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsNotAvailable(): bool
    {
        return $this->isNotAvailable;
    }

    /**
     * @param bool $isNotAvailable
     * @return Material
     */
    public function setIsNotAvailable(bool $isNotAvailable): Material
    {
		// if value is already set do nothing
        if ($this->isNotAvailable === $isNotAvailable) {
            return $this;
        }
        $this->isNotAvailable = $isNotAvailable;
        $this->publish(
            new MaterialIsNotAvailableChanged(
                $this->getId(),
                $this->getIsNotAvailable()
            )
        );
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     * @return Material
     */
    public function setIdentifier($identifier): Material
    {
        if ($this->getIdentifier() === $identifier) {
            return $this;
        }
        $this->identifier = $identifier;
        $this->publish(
            new MaterialIdentifierChanged(
                $this->getId(),
                $this->getIdentifier()
            )
        );
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @param AptoTranslatedValue $name
     * @return Material
     */
    public function setName(AptoTranslatedValue $name): Material
    {
        if ($this->getName()->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new MaterialNameChanged(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getDescription(): AptoTranslatedValue
    {
        return $this->description;
    }

    /**
     * @param AptoTranslatedValue $description
     * @return Material
     */
    public function setDescription(AptoTranslatedValue $description): Material
    {
        if ($this->getDescription()->equals($description)) {
            return $this;
        }
        $this->description = $description;
        $this->publish(
            new MaterialDescriptionChanged(
                $this->getId(),
                $this->getDescription()
            )
        );
        return $this;
    }

    /**
     * @return Material
     */
    public function incrementClicks(): Material
    {
        $this->clicks++;
        $this->publish(
            new MaterialClicksChanged(
                $this->getId(),
                $this->getClicks()
            )
        );
        return $this;
    }

    /**
     * @return int
     */
    public function getClicks(): int
    {
        return $this->clicks;
    }

    /**
     * @param int $clicks
     * @return Material
     */
    public function setClicks(int $clicks): Material
    {
        if ($this->getClicks() === $clicks) {
            return $this;
        }
        $this->clicks = $clicks;
        $this->publish(
            new MaterialClicksChanged(
                $this->getId(),
                $this->getClicks()
            )
        );
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return Material
     */
    public function setPosition(int $position): Material
    {
        if ($this->getPosition() === $position) {
            return $this;
        }
        $this->position = $position;
        $this->publish(
            new MaterialPositionChanged(
                $this->getId(),
                $this->getPosition()
            )
        );
        return $this;
    }

    /**
     * @param MediaFile $previewImage
     * @return Material
     */
    public function setPreviewImage(MediaFile $previewImage): Material
    {
        if (null !== $this->previewImage && $this->previewImage->getId()->getId() === $previewImage->getId()->getId()) {
            return $this;
        }

        $this->previewImage = $previewImage;
        $this->publish(
            new MaterialPreviewImageChanged(
                $this->getId(),
                $this->previewImage->getId()
            )
        );
        return $this;
    }

    /**
     * @return Material
     */
    public function removePreviewImage(): Material
    {
        if (null !== $this->previewImage) {
            $removedPreviewImageId = new AptoUuid($this->previewImage->getId()->getId());
            $this->previewImage = null;
            $this->publish(
                new MaterialPreviewImageRemoved(
                    $this->getId(),
                    $removedPreviewImageId
                )
            );
        }
        return $this;
    }

    /**
     * @param MediaFile $galleryImage
     * @return Material
     */
    public function addGalleryImage(MediaFile $galleryImage): Material
    {
        if (!$this->galleryImages->containsKey($galleryImage->getId()->getId())) {
            $this->galleryImages->set(
                $galleryImage->getId()->getId(),
                $galleryImage
            );
            $this->publish(
                new MaterialGalleryImageAdded(
                    $this->getId(),
                    $galleryImage->getId()
                )
            );
        }
        return $this;
    }

    /**
     * @param AptoUuid $galleryImageId
     * @return Material
     */
    public function removeGalleryImage(AptoUuid $galleryImageId): Material
    {
        if ($this->galleryImages->containsKey($galleryImageId->getId())) {
            $this->galleryImages->remove($galleryImageId->getId());
            $this->publish(
                new MaterialGalleryImageRemoved(
                    $this->getId(),
                    $galleryImageId
                )
            );
        }
        return $this;
    }

    /**
     * @param Property $property
     * @return Material
     */
    public function addProperty(Property $property)
    {
        if(!$this->properties->containsKey($property->getId()->getId())) {
            $this->properties->set(
                $property->getId()->getId(),
                $property
            );
            $this->publish(
                new MaterialPropertyAdded(
                    $this->getId(),
                    $property->getId()
                )
            );
        }

        return $this;
    }

    /**
     * @param AptoUuid $propertyId
     * @return Material
     */
    public function removeProperty(AptoUuid $propertyId)
    {
        if($this->properties->containsKey($propertyId->getId())) {
            $this->properties->remove(
                $propertyId->getId()
            );
            $this->publish(
                new MaterialPropertyRemoved(
                    $this->getId(),
                    $propertyId
                )
            );
        }

        return $this;
    }

    /**
     * @param Color $color
     * @param int $rating
     * @return Material
     */
    public function addColorRating(Color $color, int $rating): Material
    {
        $this->assertValidIntOrNullValue($rating, [0, 100]);

        $colorRatingId = $this->getNextColorRatingId();
        $colorRating = new ColorRating($colorRatingId, $this, $color, $rating);
        $this->colorRatings->set($colorRatingId->getId(), $colorRating);
        $this->publish(
            new ColorRatingAdded(
                $this->getId(),
                $colorRatingId,
                $color,
                $rating
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $colorRatingId
     * @return Material
     */
    public function removeColorRating(AptoUuid $colorRatingId): Material
    {
        if ($this->colorRatings->containsKey($colorRatingId->getId())) {
            $this->colorRatings->remove($colorRatingId->getId());
            $this->publish(
                new ColorRatingRemoved(
                    $this->getId(),
                    $colorRatingId
                )
            );
        }
        return $this;
    }

    /**
     * @param int $layer
     * @param string $perspective
     * @param MediaFile $mediaFile
     * @param Pool $pool
     * @param int $offsetX
     * @param int $offsetY
     * @return $this
     */
    public function addRenderImage(int $layer, string $perspective, MediaFile $mediaFile, Pool $pool, int $offsetX, int $offsetY): Material
    {
        $renderImageId = $this->nextRenderImageId();
        $this->renderImages->set(
            $renderImageId->getId(),
            new RenderImage(
                $renderImageId,
                $layer,
                $perspective,
                $mediaFile,
                $this,
                $pool,
                $offsetX,
                $offsetY
            )
        );
        return $this;
    }

    /**
     * @param AptoUuid $renderImageId
     * @return Material
     */
    public function removeRenderImage(AptoUuid $renderImageId): Material
    {
        if ($this->hasRenderImage($renderImageId)) {
            $this->renderImages->remove($renderImageId->getId());
        }
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasRenderImage(AptoUuid $id): bool
    {
        return $this->renderImages->containsKey($id->getId());
    }

    /**
     * @return int|null
     */
    public function getReflection()
    {
        return $this->reflection;
    }

    /**
     * @param int|null $reflection
     * @return Material
     */
    public function setReflection($reflection)
    {
        $this->assertValidIntOrNullValue($reflection, [0, 100]);

        if ($this->getReflection() !== $reflection) {
            $this->reflection = $reflection;
            $this->publish(
                new MaterialReflectionChanged(
                    $this->getId(),
                    $reflection
                )
            );
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTransmission()
    {
        return $this->transmission;
    }

    /**
     * @param int|null $transmission
     * @return Material
     */
    public function setTransmission($transmission)
    {
        $this->assertValidIntOrNullValue($transmission, [0, 100]);

        if ($this->getTransmission() !== $transmission) {
            $this->transmission = $transmission;
            $this->publish(
                new MaterialTransmissionChanged(
                    $this->getId(),
                    $transmission
                )
            );
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAbsorption()
    {
        return $this->absorption;
    }

    /**
     * @param int|null $absorption
     * @return Material
     */
    public function setAbsorption($absorption)
    {
        $this->assertValidIntOrNullValue($absorption, [0, 100]);

        if ($this->getAbsorption() !== $absorption) {
            $this->absorption = $absorption;
            $this->publish(
                new MaterialAbsorptionChanged(
                    $this->getId(),
                    $absorption
                )
            );
        }
        return $this;
    }

    /**
     * @param mixed|null $value
     * @param array|null $range
     */
    private function assertValidIntOrNullValue($value, $range = null)
    {
        if (!is_int($value) && null !== $value) {
            throw new \InvalidArgumentException('Given Value is not an integer or null.');
        }

        if (null !== $range && null !== $value) {
            if ($value < $range[0] || $value > $range[1]) {
                throw new \InvalidArgumentException('Value must be between \'' . $range[0] . '\' and \'' . $range[1] . '\'.');
            }
        }
    }

    /**
     * @return AptoUuid
     */
    private function getNextColorRatingId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @return AptoUuid
     */
    private function nextRenderImageId(): AptoUuid
    {
        return new AptoUuid();
    }
}
