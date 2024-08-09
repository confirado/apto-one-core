<?php
namespace Apto\Catalog\Domain\Core\Model\Product\Section;

use Apto\Base\Domain\Core\Model\AptoCustomProperty;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\AptoDiscount\AptoDiscounts;
use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPrices;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Catalog\Domain\Core\Model\Group\Group;
use Apto\Catalog\Domain\Core\Model\Product\Element\Element;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\RenderImageOptions;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\IdentifierUniqueException;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\Repeatable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Apto\Base\Domain\Core\Model\AptoCustomProperties;

class Section extends AptoEntity
{
    use AptoPrices;
    use AptoDiscounts;
    use AptoCustomProperties;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var bool
     */
    protected $isActive;

    /**
     * @var bool
     */
    protected $isHidden;

    /**
     * @var bool
     */
    protected $isMandatory;

    /**
     * @var bool
     */
    protected $allowMultiple;

    /**
     * @var bool
     */
    protected $isZoomable;

    /**
     * @var MediaFile|null
     */
    protected $previewImage;

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
     * @var Collection
     */
    protected $elements;

    /**
     * @var Group
     */
    protected $group;

    /**
     * @var Repeatable
     */
    protected $repeatable;

    /**
     * Section constructor.
     * @param AptoUuid $id
     * @param Product $product
     * @param Identifier $identifier
     */
    public function __construct(AptoUuid $id, Product $product, Identifier $identifier)
    {
        parent::__construct($id);
        $this->elements = new ArrayCollection();
        $this->customProperties = new ArrayCollection();
        $this->aptoPrices = new ArrayCollection();
        $this->aptoDiscounts = new ArrayCollection();
        $this->product = $product;
        $this->position = 0;
        $this->repeatable = new Repeatable(Repeatable::TYPES[0]);
        $this
            ->setIdentifier($identifier)
            ->setIsActive(false)
            ->setIsHidden(false)
            ->setIsMandatory(false)
            ->setAllowMultiple(false)
            ->setIsZoomable(false);
        $this->name = AptoTranslatedValue::fromArray([]);
        $this->description = AptoTranslatedValue::fromArray([]);
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
     * @return Section
     */
    public function setIsActive(bool $isActive): Section
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsHidden(): bool
    {
        return $this->isHidden;
    }

    /**
     * @param bool $isHidden
     * @return Section
     */
    public function setIsHidden(bool $isHidden)
    {
        $this->isHidden = $isHidden;
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
     * @return Section
     */
    public function setIsMandatory(bool $isMandatory): Section
    {
        $this->isMandatory = $isMandatory;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowMultiple(): bool
    {
        return $this->allowMultiple;
    }

    /**
     * @param bool $allowMultiple
     * @return Section
     */
    public function setAllowMultiple(bool $allowMultiple): Section
    {
        $this->allowMultiple = $allowMultiple;
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
     * @return Section
     */
    public function setIsZoomable(bool $isZoomable): Section
    {
        $this->isZoomable = $isZoomable;
        return $this;
    }

    /**
     * @param MediaFile $previewImage
     * @return Section
     */
    public function setPreviewImage(MediaFile $previewImage): Section
    {
        $this->previewImage = $previewImage;
        return $this;
    }

    /**
     * @return Section
     */
    public function removeSectionPreviewImage(): Section
    {
        $this->previewImage = null;
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
     * @return Section
     */
    public function setIdentifier(Identifier $identifier): Section
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
     * @return Section
     */
    public function setPosition(int $position): Section
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
     * @return Section
     */
    public function setName(AptoTranslatedValue $name): Section
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
     * @return Section
     */
    public function setDescription(AptoTranslatedValue $description): Section
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param Identifier $identifier
     * @return bool
     */
    public function elementIdentifierExists(Identifier $identifier): bool
    {
        /** @var Element $element */
        foreach ($this->elements as $element) {
            if ($element->getIdentifier()->equals($identifier)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasElement(AptoUuid $id): bool
    {
        return $this->elements->containsKey($id->getId());
    }

    /**
     * @param AptoUuid $id
     * @return Element|null
     */
    public function getElement(AptoUuid $id)
    {
        if ($this->hasElement($id)) {
            return $this->elements->get($id->getId());
        }
        return null;
    }

    /**
     * @param Identifier $identifier
     * @return AptoUuid|null
     */
    public function getElementIdByIdentifier(Identifier $identifier)
    {
        /** @var Element $element */
        foreach ($this->elements as $element) {
            if ($element->getIdentifier()->equals($identifier)) {
                return $element->getId();
            }
        }
        return null;
    }

    /**
     * @return Element|null
     */
    public function getDefaultElement()
    {
        /** @var Element $element */
        foreach ($this->elements as $element) {
            if ($element->getIsDefault() === true) {
                return $element;
            }
        }
        return null;
    }

    /**
     * @param Identifier $identifier
     * @param ElementDefinition|null $definition
     * @param AptoTranslatedValue|null $elementName
     * @param bool $isActive
     * @param bool $isMandatory
     * @return Section
     * @throws IdentifierUniqueException
     */
    public function addElement(Identifier $identifier, ElementDefinition $definition = null, AptoTranslatedValue $elementName = null, bool $isActive = false, bool $isMandatory = false, int $position = 0): Section
    {
        if ($this->elementIdentifierExists($identifier)) {
            throw new IdentifierUniqueException('Element Identifier must be unique within a collection!');
        }

        $elementId = $this->nextElementId();

        $this->elements->set(
            $elementId->getId(),
            new Element($elementId, $this, $identifier, $definition)
        );

        if (null !== $elementName) {
            $this->setElementName($elementId, $elementName);
        }

        $this
            ->setElementIsActive($elementId, $isActive)
            ->setElementIsMandatory($elementId, $isMandatory)
            ->setElementPosition($elementId, $position);
        return $this;
    }

    /**
     * @param AptoUuid $elementId
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
    public function addElementRenderImage(AptoUuid $elementId, int $layer, string $perspective, MediaFile $mediaFile, float $offsetX, int $offsetUnitX, float $offsetY, int $offsetUnitY, RenderImageOptions $renderImageOptions): Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->addRenderImage($layer, $perspective, $mediaFile, $offsetX, $offsetUnitX, $offsetY, $offsetUnitY, $renderImageOptions);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $name
     * @param MediaFile $mediaFile
     * @return $this
     */
    public function addElementAttachment(AptoUuid $elementId, AptoTranslatedValue $name, MediaFile $mediaFile): Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->addAttachment($name, $mediaFile);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $name
     * @param MediaFile $mediaFile
     * @return $this
     */
    public function addElementGallery(AptoUuid $elementId, AptoTranslatedValue $name, MediaFile $mediaFile): Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->addGallery($name, $mediaFile);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @return Section
     */
    public function setElementDefault(AptoUuid $elementId) : Section
    {
        $defaultElement = $this->getElement($elementId);

        if (null !== $defaultElement) {
            /** @var Element $element */
            foreach ($this->elements as $element) {
                if ($element->getIsDefault() === true) {
                    if ($element->getId()->getId() !== $defaultElement->getId()->getId()) {
                        $element->setIsDefault(false);
                        $defaultElement->setIsDefault(true);
                    }
                    return $this;
                }
            }
            $defaultElement->setIsDefault(true);
        }
        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param Identifier $newIdentifier
     * @return Section
     * @throws IdentifierUniqueException
     */
    public function setElementIdentifier(AptoUuid $elementId, Identifier $newIdentifier): Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {

            if (!$element->getIdentifier()->equals($newIdentifier) && $this->elementIdentifierExists($newIdentifier)) {
                throw new IdentifierUniqueException('Element Identifier must be unique within a collection!');
            }

            $element->setIdentifier($newIdentifier);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $name
     * @return Section
     */
    public function setElementName(AptoUuid $elementId, AptoTranslatedValue $name) : Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->setName($name);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param bool $isActive
     * @return Section
     */
    public function setElementIsActive(AptoUuid $elementId, bool $isActive) : Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->setIsActive($isActive);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param int $position
     * @return $this
     */
    public function setElementPosition(AptoUuid $elementId, int $position) : Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->setPosition($position);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param bool $isMandatory
     * @return Section
     */
    public function setElementIsMandatory(AptoUuid $elementId, bool $isMandatory) : Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->setIsMandatory($isMandatory);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $errorMessage
     * @return Section
     */
    public function setElementErrorMessage(AptoUuid $elementId, AptoTranslatedValue $errorMessage) : Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->setErrorMessage($errorMessage);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @return Section
     */
    public function setElementDefinition(AptoUuid $elementId, ElementDefinition $elementDefinition): Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->setDefinition($elementDefinition);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @return array
     */
    public function getElementSelectableValues(AptoUuid $elementId): array
    {
        $element = $this->getElement($elementId);
        if (!$element) {
            throw new \InvalidArgumentException('Section does not contain an element with Uuid \'' . $elementId->getId() . '\'.');
        }

        return $element->getDefinition()->getSelectableValues();
    }

    /**
     * @param AptoUuid $elementId
     * @return Section
     */
    public function removeElement(AptoUuid $elementId): Section
    {
        if ($this->hasElement($elementId)) {
            $this->elements->remove($elementId->getId());
        }
        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param AptoUuid $renderImageId
     * @return Section
     */
    public function removeElementRenderImage(AptoUuid $elementId, AptoUuid $renderImageId): Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->removeRenderImage($renderImageId);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param AptoUuid $attachmentId
     * @return $this
     */
    public function removeElementAttachment(AptoUuid $elementId, AptoUuid $attachmentId): Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->removeAttachment($attachmentId);
        }

        return $this;
    }

    /**
     * @param AptoUuid $elementId
     * @param AptoUuid $galleryId
     * @return $this
     */
    public function removeElementGallery(AptoUuid $elementId, AptoUuid $galleryId): Section
    {
        $element = $this->getElement($elementId);

        if (null !== $element) {
            $element->removeGallery($galleryId);
        }

        return $this;
    }

    /**
     * @return AptoUuid
     */
    private function nextElementId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @param Group $group
     * @return Section
     */
    public function setGroup(Group $group = null): Section
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return array
     */
    public function getElementIds(): array
    {
        $elementIds = [];
        /** @var Element $element */
        foreach ($this->elements as $element) {
            $elementIds[] = $element->getId();
        }
        return $elementIds;
    }

    /**
     * @return Repeatable
     */
    public function getRepeatable(): Repeatable
    {
        return $this->repeatable;
    }

    /**
     * @param Repeatable $repeatable
     *
     * @return $this
     */
    public function setRepeatable(Repeatable $repeatable): Section
    {
        $this->repeatable = $repeatable;
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @param Collection $entityMapping
     * @param Identifier|null $identifier
     * @return Section
     * @throws AptoCustomPropertyException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function copy(AptoUuid $id, Collection &$entityMapping, ?Identifier $identifier = null): Section
    {
        // create new section
        $section = new Section(
            $id,
            $entityMapping->get($this->product->getId()->getId()),
            $identifier ? $identifier : $this->getIdentifier()
        );

        // add new section to entityMapping
        $entityMapping->set(
            $this->getId()->getId(),
            $section
        );

        // set customProperties
        $section->customProperties = $this->copyAptoCustomProperties();

        // set prices
        $section->aptoPrices = $this->copyAptoPrices();

        // set discounts
        $section->aptoDiscounts = $this->copyAptoDiscounts();

        // set elements
        $section->elements = $this->copyElements($entityMapping);

        // set properties
        $section
            ->setIsActive($this->getIsActive())
            ->setIsMandatory($this->getIsMandatory())
            ->setName($this->getName())
            ->setDescription($this->getDescription())
            ->setIsHidden($this->getIsHidden())
            ->setAllowMultiple($this->getAllowMultiple())
            ->setPosition($this->getPosition())
            ->setGroup($this->group)
            ->setIsZoomable($this->getIsZoomable())
            ->setRepeatable($this->getRepeatable());

        if (null !== $this->previewImage) {
            $section->setPreviewImage($this->previewImage);
        }

        // return new section
        return $section;
    }

    /**
     * @param Collection $entityMapping
     * @return void
     */
    public function afterConditionSetsCopied(Collection &$entityMapping): void
    {
        /** @var AptoCustomProperty $customProperty */
        foreach ($this->customProperties as $customProperty) {
            if ($customProperty->getProductConditionId() === null) {
                continue;
            }

            $customProperty->setProductConditionId($entityMapping->get($customProperty->getProductConditionId()->getId())->getId());
        }

        /** @var Element $element */
        foreach ($this->elements as $element) {
            $element->afterConditionSetsCopied($entityMapping);
        }
    }

    /**
     * @param AptoUuid $elementId
     * @param Collection $entityMapping
     * @return AptoUuid
     * @throws AptoCustomPropertyException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function copyElement(AptoUuid $elementId, Collection &$entityMapping): ?AptoUuid
    {
        // get element
        $element = $this->getElement($elementId);
        if (null === $element) {
            return null;
        }

        // set entity mapping
        $entityMapping->set(
            $this->getId()->getId(), $this
        );

        // copy element
        $newElementId = $this->nextElementId();
        $this->elements->set(
            $newElementId->getId(),
            $element->copy(
                $newElementId,
                $entityMapping,
                new Identifier($newElementId->getId())
            )
        );

        // return section
        return $newElementId;
    }

    /**
     * @param Collection $entityMapping
     * @return Collection
     */
    private function copyElements(Collection &$entityMapping): Collection
    {
        $collection = new ArrayCollection();

        /** @var Element $element */
        foreach ($this->elements as $element) {
            $elementId = $this->nextElementId();

            $collection->set(
                $elementId->getId(),
                $element->copy($elementId, $entityMapping)
            );
        }

        return $collection;
    }
}
