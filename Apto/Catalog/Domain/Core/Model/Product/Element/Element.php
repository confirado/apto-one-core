<?php
namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\AptoDiscount\AptoDiscounts;
use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPrices;
use Apto\Base\Domain\Core\Model\AptoPriceFormula\AptoPriceFormulas;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrix;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Section\Section;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Element extends AptoEntity
{
    use AptoPrices;
    use AptoPriceFormulas;
    use AptoDiscounts;
    use AptoCustomProperties;

    /**
     * @var Section
     */
    protected $section;

    /**
     * @var bool
     */
    protected $isActive;

    /**
     * @var bool
     */
    protected $isDefault;

    /**
     * @var bool
     */
    protected $isMandatory;

    /**
     * @var bool
     */
    protected $isNotAvailable;

    /**
     * @var bool
     */
    protected $isZoomable;

    /**
     * @var Identifier
     */
    protected $identifier;

    /**
     * @var int
     */
    private $position;

    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var AptoTranslatedValue
     */
    protected $description;

    /**
     * @var AptoTranslatedValue
     */
    protected $errorMessage;

    /**
     * @var ElementDefinition
     */
    protected $definition;

    /**
     * @var Collection
     */
    protected $renderImages;

    /**
     * @var Collection
     */
    protected $attachments;

    /**
     * @var Collection
     */
    protected $gallery;

    /**
     * @var MediaFile|null
     */
    protected $previewImage;

    /**
     * @var float
     */
    protected $percentageSurcharge;

    /**
     * @var bool
     */
    private $priceMatrixActive;

    /**
     * @var PriceMatrix|null
     */
    private $priceMatrix;

    /**
     * @var string
     */
    private $priceMatrixRow;

    /**
     * @var string
     */
    private $priceMatrixColumn;

    /**
     * @var bool
     */
    private $extendedPriceCalculationActive;

    /**
     * @var string
     */
    private $extendedPriceCalculationFormula;

    /**
     * @var ZoomFunction
     */
    private ZoomFunction $zoomFunction;

    /**
     * @var bool
     */
    private $openLinksInDialog;

    /**
     * Element constructor.
     * @param AptoUuid $id
     * @param Section $section
     * @param Identifier $identifier
     * @param ElementDefinition|null $definition
     */
    public function __construct(
        AptoUuid $id,
        Section $section,
        Identifier $identifier,
        ElementDefinition $definition = null
    ) {
        parent::__construct($id);
        $this->customProperties = new ArrayCollection();
        $this->aptoPrices = new ArrayCollection();
        $this->aptoPriceFormulas = new ArrayCollection();
        $this->aptoDiscounts = new ArrayCollection();
        $this->section = $section;
        $this->position = 0;
        $this->percentageSurcharge = 0.0;
        $this
            ->setIsActive(false)
            ->setIsDefault(false)
            ->setIdentifier($identifier)
            ->setDefinition($definition ?? new DefaultElementDefinition());
        $this->renderImages = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->gallery = new ArrayCollection();

        $this->isMandatory = false;
        $this->isNotAvailable = false;
        $this->isZoomable = false;
        $this->priceMatrix = null;
        $this->priceMatrixActive = false;
        $this->priceMatrixRow = '';
        $this->priceMatrixColumn = '';

        $this->extendedPriceCalculationActive = false;
        $this->extendedPriceCalculationFormula = '';
        $this->openLinksInDialog = false;
        $this->zoomFunction = new ZoomFunction();
        $this->name = AptoTranslatedValue::fromArray([]);
        $this->description = AptoTranslatedValue::fromArray([]);
        $this->errorMessage = AptoTranslatedValue::fromArray([]);
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return Element
     */
    public function setIsActive(bool $isActive): Element
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     * @return Element
     */
    public function setIsDefault(bool $isDefault): Element
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsMandatory(): bool
    {
        return $this->isMandatory;
    }

    /**
     * @param bool $isMandatory
     * @return Element
     */
    public function setIsMandatory(bool $isMandatory): Element
    {
        $this->isMandatory = $isMandatory;
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
     * @return Element
     */
    public function setIsNotAvailable(bool $isNotAvailable): Element
    {
        $this->isNotAvailable = $isNotAvailable;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsZoomable(): bool
    {
        return $this->isZoomable;
    }

    /**
     * @param bool $isZoomable
     * @return Element
     */
    public function setIsZoomable(bool $isZoomable): Element
    {
        $this->isZoomable = $isZoomable;
        return $this;
    }

    /**
     * @return Identifier
     */
    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @param Identifier $identifier
     * @return Element
     */
    public function setIdentifier(Identifier $identifier): Element
    {
        $this->identifier = $identifier;
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
     * @return Element
     */
    public function setPosition(int $position): Element
    {
        $this->position = $position;
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
     * @return Element
     */
    public function setName(AptoTranslatedValue $name): Element
    {
        $this->name = $name;
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
     * @return Element
     */
    public function setDescription(AptoTranslatedValue $description): Element
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getErrorMessage(): AptoTranslatedValue
    {
        return $this->errorMessage;
    }

    /**
     * @param AptoTranslatedValue $errorMessage
     * @return Element
     */
    public function setErrorMessage(AptoTranslatedValue $errorMessage): Element
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @return ElementDefinition
     */
    public function getDefinition(): ElementDefinition
    {
        return $this->definition;
    }

    /**
     * @param ElementDefinition $definition
     * @return Element
     */
    public function setDefinition(ElementDefinition $definition): Element
    {
        $this->definition = $definition;
        return $this;
    }

    /**
     * @return array
     */
    public function getRenderImages(): array
    {
        return $this->renderImages->toArray();
    }

    /**
     * @param string $perspective
     * @return array
     */
    public function getRenderImagesByPerspective(string $perspective): array
    {
        $renderImages = [];

        /** @var RenderImage $renderImage */
        foreach ($this->renderImages as $renderImage) {
            if ($renderImage->getPerspective() === $perspective) {
                $renderImages[] = $renderImage;
            }
        }

        return $renderImages;
    }

    /**
     * @param int $layer
     * @param string $perspective
     * @param MediaFile $mediaFile
     * @param float $offsetX
     * @param int $offsetUnitX
     * @param float $offsetY
     * @param int $offsetUnitY
     * @param RenderImageOptions $renderImageOptions
     * @return $this
     */
    public function addRenderImage(int $layer, string $perspective, MediaFile $mediaFile, float $offsetX, int $offsetUnitX, float $offsetY, int $offsetUnitY, RenderImageOptions $renderImageOptions): Element
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
                $offsetX,
                $offsetUnitX,
                $offsetY,
                $offsetUnitY,
                $renderImageOptions
            )
        );
        return $this;
    }

    /**
     * @param AptoUuid $renderImageId
     * @return Element
     */
    public function removeRenderImage(AptoUuid $renderImageId): Element
    {
        if ($this->hasRenderImage($renderImageId)) {
            $this->renderImages->remove($renderImageId->getId());
        }
        return $this;
    }

    /**
     * @return self
     */
    public function clearRenderImages(): self
    {
        $this->renderImages->clear();
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
     * @return AptoUuid
     */
    private function nextRenderImageId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @param AptoTranslatedValue $name
     * @param MediaFile $mediaFile
     * @return $this
     */
    public function addAttachment(AptoTranslatedValue $name, MediaFile $mediaFile): Element
    {
        $attachmentId = $this->nextAttachmentId();

        $this->attachments->set(
            $attachmentId->getId(),
            new Attachment(
                $attachmentId,
                $name,
                $mediaFile,
                $this
            )
        );
        return $this;
    }

    /**
     * @param AptoTranslatedValue $name
     * @param MediaFile $mediaFile
     * @return $this
     */
    public function addGallery(AptoTranslatedValue $name, MediaFile $mediaFile): Element
    {
        $galleryId = $this->nextGalleryId();

        $this->gallery->set(
            $galleryId->getId(),
            new Gallery(
                $galleryId,
                $name,
                $mediaFile,
                $this
            )
        );
        return $this;
    }

    /**
     * @param AptoUuid $attachmentId
     * @return $this
     */
    public function removeAttachment(AptoUuid $attachmentId): Element
    {
        if ($this->hasAttachment($attachmentId)) {
            $this->attachments->remove($attachmentId->getId());
        }
        return $this;
    }

    /**
     * @param AptoUuid $galleryId
     * @return $this
     */
    public function removeGallery(AptoUuid $galleryId): Element
    {
        if ($this->hasGallery($galleryId)) {
            $this->gallery->remove($galleryId->getId());
        }
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasAttachment(AptoUuid $id): bool
    {
        return $this->attachments->containsKey($id->getId());
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasGallery(AptoUuid $id): bool
    {
        return $this->gallery->containsKey($id->getId());
    }

    /**
     * @return AptoUuid
     */
    private function nextAttachmentId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @return AptoUuid
     */
    private function nextGalleryId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @param MediaFile $previewImage
     * @return Element
     */
    public function setPreviewImage(MediaFile $previewImage): Element
    {
        $this->previewImage = $previewImage;
        return $this;
    }

    /**
     * @return Element
     */
    public function removeElementPreviewImage(): Element
    {
        $this->previewImage = null;
        return $this;
    }

    /**
     * @return float
     */
    public function getPercentageSurcharge(): float
    {
        return $this->percentageSurcharge;
    }

    /**
     * @param float $percentageSurcharge
     * @return Element
     */
    public function setPercentageSurcharge(float $percentageSurcharge): Element
    {
        $this->percentageSurcharge = $percentageSurcharge;
        return $this;
    }

    /**
     * @return bool
     */
    public function getPriceMatrixActive(): bool
    {
        return $this->priceMatrixActive;
    }

    /**
     * @param bool $priceMatrixActive
     * @return Element
     */
    public function setPriceMatrixActive(bool $priceMatrixActive): Element
    {
        $this->priceMatrixActive = $priceMatrixActive;
        return $this;
    }

    /**
     * @return PriceMatrix
     */
    public function getPriceMatrix(): ?PriceMatrix
    {
        return $this->priceMatrix;
    }

    /**
     * @param PriceMatrix|null $priceMatrix
     * @return Element
     */
    public function setPriceMatrix(?PriceMatrix $priceMatrix): Element
    {
        $this->priceMatrix = $priceMatrix;
        return $this;
    }

    /**
     * @return string
     */
    public function getPriceMatrixRow(): string
    {
        return $this->priceMatrixRow;
    }

    /**
     * @param string $priceMatrixRow
     * @return Element
     */
    public function setPriceMatrixRow(string $priceMatrixRow): Element
    {
        $this->priceMatrixRow = $priceMatrixRow;
        return $this;
    }

    /**
     * @return string
     */
    public function getPriceMatrixColumn(): string
    {
        return $this->priceMatrixColumn;
    }

    /**
     * @param string $priceMatrixColumn
     * @return Element
     */
    public function setPriceMatrixColumn(string $priceMatrixColumn): Element
    {
        $this->priceMatrixColumn = $priceMatrixColumn;
        return $this;
    }

    /**
     * @return bool
     */
    public function getExtendedPriceCalculationActive(): bool
    {
        return $this->extendedPriceCalculationActive;
    }

    /**
     * @param bool $extendedPriceCalculationActive
     * @return Element
     */
    public function setExtendedPriceCalculationActive(bool $extendedPriceCalculationActive): Element
    {
        $this->extendedPriceCalculationActive = $extendedPriceCalculationActive;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtendedPriceCalculationFormula(): string
    {
        return $this->extendedPriceCalculationFormula;
    }

    /**
     * @param string $extendedPriceCalculationFormula
     * @return Element
     */
    public function setExtendedPriceCalculationFormula(string $extendedPriceCalculationFormula): Element
    {
        $this->extendedPriceCalculationFormula = $extendedPriceCalculationFormula;
        return $this;
    }

    /**
     * @param bool $active
     * @param PriceMatrix|null $priceMatrix
     * @param string|null $row
     * @param string|null $column
     * @return $this
     */
    public function setElementPriceMatrix(bool $active, ?PriceMatrix $priceMatrix, ?string $row, ?string $column): Element
    {
        $this->priceMatrixActive = $active;
        $this->priceMatrix = $priceMatrix;
        $this->priceMatrixRow = $row;
        $this->priceMatrixColumn = $column;
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @param Collection $entityMapping
     * @param Identifier|null $identifier
     * @return Element
     * @throws AptoCustomPropertyException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function copy(AptoUuid $id, Collection &$entityMapping, ?Identifier $identifier = null): Element
    {
        // create new element
        $element = new Element(
            $id,
            $entityMapping->get($this->section->getId()->getId()),
            $identifier ? $identifier : $this->getIdentifier(),
            $this->getDefinition()
        );

        // add new element to entityMapping
        $entityMapping->set(
            $this->getId()->getId(),
            $element
        );

        // set customProperties
        $element->customProperties = $this->copyAptoCustomProperties();

        // set prices
        $element->aptoPrices = $this->copyAptoPrices();

        // set price formulas
        $element->aptoPriceFormulas = $this->copyAptoPriceFormulas();

        // set discounts
        $element->aptoDiscounts = $this->copyAptoDiscounts();

        // set renderImages
        $element->renderImages = $this->copyRenderImages($entityMapping);

        // set properties
        $element
            ->setIsActive($this->getIsActive())
            ->setIsDefault($this->getIsDefault())
            ->setIsMandatory($this->getIsMandatory())
            ->setIsNotAvailable($this->getIsNotAvailable())
            ->setIsZoomable($this->getIsZoomable())
            ->setZoomFunction($this->getZoomFunction())
            ->setName($this->getName())
            ->setDescription($this->getDescription())
            ->setErrorMessage($this->getErrorMessage())
            ->setPosition($this->getPosition())
            ->setPercentageSurcharge($this->getPercentageSurcharge())
            ->setPriceMatrixActive($this->getPriceMatrixActive())
            ->setPriceMatrix($this->getPriceMatrix())
            ->setPriceMatrixRow($this->getPriceMatrixRow())
            ->setPriceMatrixColumn($this->getPriceMatrixColumn())
            ->setExtendedPriceCalculationActive($this->getExtendedPriceCalculationActive())
            ->setExtendedPriceCalculationFormula($this->getExtendedPriceCalculationFormula())
            ->setOpenLinksInDialog($this->getOpenLinksInDialog());

        if (null !== $this->previewImage) {
            $element->setPreviewImage($this->previewImage);
        }

        // return new element
        return $element;
    }

    /**
     * @param Collection $entityMapping
     * @return Collection
     */
    private function copyRenderImages(Collection &$entityMapping): Collection
    {
        $collection = new ArrayCollection();

        /** @var RenderImage $renderImage */
        foreach ($this->renderImages as $renderImage) {
            $renderImageId = $this->nextRenderImageId();

            $collection->set(
                $renderImageId->getId(),
                $renderImage->copy($renderImageId, $entityMapping)
            );
        }

        return $collection;
    }

    /**
     * @param Collection $entityMapping
     * @return void
     */
    public function copyRenderImageOptions(Collection &$entityMapping)
    {
        /** @var RenderImage $renderImage */
        foreach ($this->renderImages as $renderImage) {
            $renderImage->copyRenderImageOptions($entityMapping);
        }
    }

    /**
     * @return ZoomFunction
     */
    public function getZoomFunction(): ZoomFunction
    {
        return $this->zoomFunction;
    }

    /**
     * @param ZoomFunction $zoomFunction
     * @return Element
     */
    public function setZoomFunction(ZoomFunction $zoomFunction): Element
    {
        $this->zoomFunction = $zoomFunction;
        return $this;
    }

    /**
     * @return bool
     */
    public function getOpenLinksInDialog(): bool
    {
        return $this->openLinksInDialog;
    }

    /**
     * @param bool $openLinksInDialog
     * @return Element
     */
    public function setOpenLinksInDialog(bool $openLinksInDialog): Element
    {
        $this->openLinksInDialog = $openLinksInDialog;
        return $this;
    }
}
