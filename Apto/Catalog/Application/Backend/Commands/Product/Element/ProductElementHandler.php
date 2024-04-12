<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Base\Domain\Core\Model\AptoPriceFormula\AptoPriceFormulaDuplicateException;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixRepository;
use Apto\Catalog\Domain\Core\Model\Product\Element\ZoomFunction;
use Exception;
use Money\Currency;
use Money\Money;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;
use Apto\Catalog\Application\Backend\Commands\Product\ProductChildHandler;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\ElementDefinitionRegistry;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Product\IdentifierUniqueException;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\AptoDiscount\AptoDiscountDuplicateException;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;

class ProductElementHandler extends ProductChildHandler
{
    /**
     * @var ElementDefinitionRegistry
     */
    private $elementDefinitionRegistry;

    /**
     * @var MediaFileRepository
     */
    protected $mediaFileRepository;

    /**
     * @var PriceMatrixRepository
     */
    protected $priceMatrixRepository;

    /**
     * @var MediaFileSystemConnector
     */
    protected $fileSystemConnector;

    /**
     * ProductElementHandler constructor.
     * @param ProductRepository $productRepository
     * @param ElementDefinitionRegistry $elementDefinitionRegistry
     * @param MediaFileRepository $mediaFileRepository
     * @param PriceMatrixRepository $priceMatrixRepository
     * @param MediaFileSystemConnector $fileSystemConnector
     */
    public function __construct(
        ProductRepository $productRepository,
        ElementDefinitionRegistry $elementDefinitionRegistry,
        MediaFileRepository $mediaFileRepository,
        PriceMatrixRepository $priceMatrixRepository,
        MediaFileSystemConnector $fileSystemConnector
    ) {
        parent::__construct($productRepository);
        $this->elementDefinitionRegistry = $elementDefinitionRegistry;
        $this->mediaFileRepository = $mediaFileRepository;
        $this->priceMatrixRepository = $priceMatrixRepository;
        $this->fileSystemConnector = $fileSystemConnector;
    }

    /**
     * @param AddProductElement $command
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleAddProductElement(AddProductElement $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            throw new \InvalidArgumentException('Product not found.');
        }

        $elementName = $command->getElementName();
        if (null !== $elementName) {
            $elementName = $this->getTranslatedValue($elementName);
        }

        $identifier = $this->getIdentifier($command->getElementIdentifier(), $elementName);
        if (null === $identifier) {
            throw new \InvalidArgumentException('Could not generate any Identifier because Identifier and Name are empty!');
        }
        $sectionId = new AptoUuid($command->getSectionId());
        $position = $this->productRepository->findNextElementPosition($product->getId()->getId(), $sectionId);

        $identifier = $this->findNextElementIdentifier($product, $identifier, $sectionId);
        $product->addElement(
            $sectionId,
            $identifier,
            null,
            $elementName,
            $command->getIsActive(),
            $command->getIsMandatory(),
            $position
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param UpdateProductElement $command
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleUpdateProductElement(UpdateProductElement $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            throw new \InvalidArgumentException('Product not found.');
        }

        $elementName = $command->getElementName();
        if (null !== $elementName) {
            $elementName = $this->getTranslatedValue($elementName);
        }

        $identifier = $this->getIdentifier($command->getElementIdentifier(), $elementName);
        if (null === $identifier) {
            throw new \InvalidArgumentException('Could not generate any Identifier because Identifier and Name are empty!');
        }

        $sectionId = new AptoUuid($command->getSectionId());
        $elementId = new AptoUuid($command->getElementId());
        $identifier = $this->findNextElementIdentifier($product, $identifier, $sectionId, $elementId);
        $product
            ->setElementIdentifier(
                $sectionId,
                $elementId,
                $identifier
            )
            ->setElementName(
                $sectionId,
                $elementId,
                $this->getTranslatedValue($command->getElementName())
            )
            ->setElementDescription(
                $sectionId,
                $elementId,
                $this->getTranslatedValue($command->getElementDescription())
            )
            ->setElementErrorMessage(
                $sectionId,
                $elementId,
                $this->getTranslatedValue($command->getElementErrorMessage())
            )
            ->setElementPosition(
                $sectionId,
                $elementId,
                $command->getPosition()
            )
            ->setElementPercentageSurcharge(
                $sectionId,
                $elementId,
                $command->getPercentageSurcharge()
            )
            ->setElementPriceMatrix(
                $sectionId,
                $elementId,
                $command->getPriceMatrixActive(),
                $this->priceMatrixRepository->findById($command->getPriceMatrixId()),
                $command->getPriceMatrixRow(),
                $command->getPriceMatrixColumn()
            )
            ->setElementExtendedPriceCalculationActive(
                $sectionId,
                $elementId,
                $command->getExtendedPriceCalculationActive()
            )->setElementExtendedPriceCalculationFormula(
                $sectionId,
                $elementId,
                $command->getExtendedPriceCalculationFormula()
            )
        ;

        if (null !== $command->getIsActive()) {
            $product->setElementIsActive(
                $sectionId,
                $elementId,
                $command->getIsActive()
            );
        }

        if (null !== $command->getIsMandatory()) {
            $product->setElementIsMandatory(
                $sectionId,
                $elementId,
                $command->getIsMandatory()
            );
        }

        if (null !== $command->getIsNotAvailable())        {
            $product->setElementIsNotAvailable(
                $sectionId,
                $elementId,
                $command->getIsNotAvailable()
            );
        }

        if (null !== $command->getIsZoomable()) {
            $product->setElementIsZoomable(
              $sectionId,
              $elementId,
              $command->getIsZoomable()
            );
        }

        if (null !== $command->getZoomFunction()) {
            $product->setElementZoomFunction(
              $sectionId,
              $elementId,
              new ZoomFunction($command->getZoomFunction())
            );
        }

        if (null !== $command->getOpenLinksInDialog()) {
            $product->setOpenLinksInDialog(
                $sectionId,
                $elementId,
                $command->getOpenLinksInDialog()
            );
        }

        if (null !== $command->getIsDefault()) {
            $product->setElementIsDefault(
                $sectionId,
                $elementId,
                $command->getIsDefault()
            );
        }

        $definition = $command->getDefinition();

        if (isset($definition['className']) && isset($definition['values'])) {
            $registeredElementDefinition = $this->elementDefinitionRegistry->getRegisteredElementDefinition($definition['className']);
            $elementDefinition = $registeredElementDefinition->getElementDefinition($definition['values']);

            $product->setElementDefinition(
                $sectionId,
                $elementId,
                $elementDefinition
            );
        }

        if ($command->getPreviewImage()) {
            $mediaFile = $this->getMediaFile($command->getPreviewImage());
            $product->setElementPreviewImage(
                $sectionId,
                $elementId,
                $mediaFile
            );
        } else {
            $product->removeElementPreviewImage(
                $sectionId,
                $elementId
            );
        }

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductElement $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductElement(RemoveProductElement $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->removeElement(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            )
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductElementRenderImage $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductElementRenderImage(RemoveProductElementRenderImage $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->removeElementRenderImage(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            new AptoUuid(
                $command->getRenderImageId()
            )
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductElementAttachment $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductElementAttachment(RemoveProductElementAttachment $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->removeElementAttachment(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            new AptoUuid(
                $command->getAttachmentId()
            )
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductElementGallery $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductElementGallery(RemoveProductElementGallery $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->removeElementGallery(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            new AptoUuid(
                $command->getGalleryId()
            )
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param AddProductElementCustomProperty $command
     * @throws InvalidUuidException
     */
    public function handleAddProductElementCustomProperty(AddProductElementCustomProperty $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->addElementCustomProperty(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            $command->getKey(),
            $command->getValue(),
            $command->getTranslatable()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductElementCustomProperty $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductElementCustomProperty(RemoveProductElementCustomProperty $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->removeElementCustomProperty(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            $command->getKey()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param SetProductElementIsDefault $command
     * @throws InvalidUuidException
     */
    public function handleSetProductElementIsDefault(SetProductElementIsDefault $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->setElementIsDefault(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            $command->getIsDefault()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param SetProductElementIsActive $command
     * @throws InvalidUuidException
     */
    public function handleSetProductElementIsActive(SetProductElementIsActive $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->setElementIsActive(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            $command->getIsActive()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param SetProductElementIsMandatory $command
     * @throws InvalidUuidException
     */
    public function handleSetProductElementIsMandatory(SetProductElementIsMandatory $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->setElementIsMandatory(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            $command->getIsMandatory()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param AddProductElementPrice $command
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function handleAddProductElementPrice(AddProductElementPrice $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->addElementPrice(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            new Money(
                $command->getAmount(),
                new Currency(
                    $command->getCurrency()
                )
            ),
            new AptoUuid(
                $command->getCustomerGroupId()
            ),
            $command->getProductConditionId() ? new AptoUuid($command->getProductConditionId()) : null
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductElementPrice $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductElementPrice(RemoveProductElementPrice $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null !== $product) {
            $product->removeElementPrice(
                new AptoUuid(
                    $command->getSectionId()
                ),
                new AptoUuid(
                    $command->getElementId()
                ),
                new AptoUuid(
                    $command->getPriceId()
                )
            );
            $this->productRepository->update($product);
            $product->publishEvents();
        }
    }

    /**
     * @param AddProductElementPriceFormula $command
     * @throws AptoPriceFormulaDuplicateException
     * @throws InvalidUuidException
     */
    public function handleAddProductElementPriceFormula(AddProductElementPriceFormula $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->addElementPriceFormula(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            $command->getFormula(),
            new Currency(
                $command->getCurrency()
            ),
            new AptoUuid(
                $command->getCustomerGroupId()
            ),
            $command->getProductConditionId() ? new AptoUuid($command->getProductConditionId()) : null
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductElementPriceFormula $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductElementPriceFormula(RemoveProductElementPriceFormula $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null !== $product) {
            $product->removeElementPriceFormula(
                new AptoUuid(
                    $command->getSectionId()
                ),
                new AptoUuid(
                    $command->getElementId()
                ),
                new AptoUuid(
                    $command->getPriceFormulaId()
                )
            );
            $this->productRepository->update($product);
            $product->publishEvents();
        }
    }

    /**
     * @param AddProductElementDiscount $command
     * @throws AptoDiscountDuplicateException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function handleAddProductElementDiscount(AddProductElementDiscount $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->addElementDiscount(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            $command->getDiscount(),
            new AptoUuid(
                $command->getCustomerGroupId()
            ),
            $this->getTranslatedValue($command->getName())
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductElementDiscount $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductElementDiscount(RemoveProductElementDiscount $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        $product->removeElementDiscount(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getElementId()
            ),
            new AptoUuid(
                $command->getDiscountId()
            )
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param string $path
     * @return MediaFile
     * @throws InvalidUuidException
     */
    protected function getMediaFile(string $path): MediaFile
    {
        $file = File::createFromPath($path);

        $mediaFile = $this->mediaFileRepository->findOneByFile($file);
        if (null === $mediaFile) {
            $mediaFile = new MediaFile(
                $this->mediaFileRepository->nextIdentity(),
                $file
            );
            $mediaFile
                ->setSize($this->fileSystemConnector->getFileSize($file))
                ->setMd5Hash($this->fileSystemConnector->getFileMd5Hash($file));

            $this->mediaFileRepository->add($mediaFile);
            $mediaFile->publishEvents();
        }

        return $mediaFile;
    }

    /**
     * @param Product $product
     * @param Identifier $identifier
     * @param AptoUuid $sectionId
     * @param AptoUuid|null $elementId
     * @return Identifier
     * @throws Exception
     */
    protected function findNextElementIdentifier(Product $product, Identifier $identifier, AptoUuid $sectionId, ?AptoUuid $elementId = null): Identifier
    {
        $identifierSearchCount = 0;

        while ($product->elementIdentifierExists($sectionId, $identifier) && $identifierSearchCount < 100) {
            $identifierSearchCount++;

            if (null !== $elementId && $this->isOwnElementIdentifier($product, $sectionId, $elementId, $identifier)) {
                return $identifier;
            }

            if ($identifierSearchCount === 1) {
                $identifier = new Identifier($identifier->getValue() . '-' . $identifierSearchCount);
                continue;
            }

            $previousIdentifierValue = $identifier->getValue();
            $orgIdentifierLength = strrpos($previousIdentifierValue, '-');
            $newIdentifierValue = substr($previousIdentifierValue, 0, $orgIdentifierLength + 1) . $identifierSearchCount;
            $identifier = new Identifier($newIdentifierValue);
        }

        return $identifier;
    }

    /**
     * @param Product $product
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param Identifier $identifier
     * @return bool
     */
    private function isOwnElementIdentifier(Product $product, AptoUuid $sectionId, AptoUuid $elementId, Identifier $identifier): bool
    {
        $identifierId = $product->getElementIdByIdentifier($sectionId, $identifier);
        if (null !== $identifierId && $elementId->getId() === $identifierId->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddProductElement::class => [
            'method' => 'handleAddProductElement',
            'bus' => 'command_bus'
        ];

        yield UpdateProductElement::class => [
            'method' => 'handleUpdateProductElement',
            'bus' => 'command_bus'
        ];

        yield RemoveProductElement::class => [
            'method' => 'handleRemoveProductElement',
            'bus' => 'command_bus'
        ];

        yield RemoveProductElementRenderImage::class => [
            'method' => 'handleRemoveProductElementRenderImage',
            'bus' => 'command_bus'
        ];

        yield RemoveProductElementAttachment::class => [
            'method' => 'handleRemoveProductElementAttachment',
            'bus' => 'command_bus'
        ];

        yield RemoveProductElementGallery::class => [
            'method' => 'handleRemoveProductElementGallery',
            'bus' => 'command_bus'
        ];

        yield AddProductElementCustomProperty::class => [
            'method' => 'handleAddProductElementCustomProperty',
            'bus' => 'command_bus'
        ];

        yield RemoveProductElementCustomProperty::class => [
            'method' => 'handleRemoveProductElementCustomProperty',
            'bus' => 'command_bus'
        ];

        yield SetProductElementIsDefault::class => [
            'method' => 'handleSetProductElementIsDefault',
            'bus' => 'command_bus'
        ];

        yield SetProductElementIsActive::class => [
            'method' => 'handleSetProductElementIsActive',
            'bus' => 'command_bus'
        ];

        yield SetProductElementIsMandatory::class => [
            'method' => 'handleSetProductElementIsMandatory',
            'bus' => 'command_bus'
        ];

        yield AddProductElementPrice::class => [
            'method' => 'handleAddProductElementPrice',
            'bus' => 'command_bus'
        ];

        yield RemoveProductElementPrice::class => [
            'method' => 'handleRemoveProductElementPrice',
            'bus' => 'command_bus'
        ];

        yield AddProductElementPriceFormula::class => [
            'method' => 'handleAddProductElementPriceFormula',
            'bus' => 'command_bus'
        ];

        yield RemoveProductElementPriceFormula::class => [
            'method' => 'handleRemoveProductElementPriceFormula',
            'bus' => 'command_bus'
        ];

        yield AddProductElementDiscount::class => [
            'method' => 'handleAddProductElementDiscount',
            'bus' => 'command_bus'
        ];

        yield RemoveProductElementDiscount::class => [
            'method' => 'handleRemoveProductElementDiscount',
            'bus' => 'command_bus'
        ];
    }
}
