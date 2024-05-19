<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Catalog\Domain\Core\Model\Product\Repeatable;
use Exception;
use Money\Currency;
use Money\Money;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Backend\Commands\Product\ProductChildHandler;
use Apto\Catalog\Domain\Core\Model\Group\GroupRepository;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Product\IdentifierUniqueException;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\AptoDiscount\AptoDiscountDuplicateException;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;

class ProductSectionHandler extends ProductChildHandler
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var MediaFileRepository
     */
    protected $mediaFileRepository;

    /**
     * @var MediaFileSystemConnector
     */
    protected $fileSystemConnector;

    /**
     * ProductSectionHandler constructor.
     * @param ProductRepository $productRepository
     * @param GroupRepository $groupRepository
     * @param MediaFileRepository $mediaFileRepository
     * @param MediaFileSystemConnector $fileSystemConnector
     */
    public function __construct(ProductRepository $productRepository, GroupRepository $groupRepository, MediaFileRepository $mediaFileRepository, MediaFileSystemConnector $fileSystemConnector)
    {
        parent::__construct($productRepository);
        $this->groupRepository = $groupRepository;
        $this->mediaFileRepository = $mediaFileRepository;
        $this->fileSystemConnector = $fileSystemConnector;
    }

    /**
     * @param AddProductSection $command
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleAddProductSection(AddProductSection $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            throw new \InvalidArgumentException('Product not found.');
        }

        $sectionName = $command->getSectionName();
        if (null !== $sectionName) {
            $sectionName = $this->getTranslatedValue($sectionName);
        }

        $identifier = $this->getIdentifier($command->getSectionIdentifier(), $sectionName);
        if (null === $identifier) {
            throw new \InvalidArgumentException('Could not generate any Identifier because Identifier and Name are empty!');
        }

        $position = $this->productRepository->findNextSectionPosition($product->getId()->getId());

        $identifier = $this->findNextSectionIdentifier($product, $identifier);
        $product->addSection(
            $identifier,
            $sectionName,
            $command->getActive(),
            $position,
            $command->getAddDefaultElement(),
        );
        $this->productRepository->update($product);
    }

    /**
     * @param UpdateProductSection $command
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleUpdateProductSection(UpdateProductSection $command)
    {
        $sectionId = new AptoUuid($command->getSectionId());

        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            throw new \InvalidArgumentException('Product not found.');
        }

        $sectionName = $command->getSectionName();
        if (null !== $sectionName) {
            $sectionName = $this->getTranslatedValue($sectionName);
        }

        $identifier = $this->getIdentifier($command->getSectionIdentifier(), $sectionName);
        if (null === $identifier) {
            throw new \InvalidArgumentException('Could not generate any Identifier because Identifier and Name are empty!');
        }

        $identifier = $this->findNextSectionIdentifier($product, $identifier, $sectionId);
        $product
            ->setSectionIdentifier(
                $sectionId,
                $identifier
            )
            ->setSectionName(
                $sectionId,
                $sectionName
            )
            ->setSectionDescription(
                $sectionId,
                $this->getTranslatedValue(
                    $command->getSectionDescription()
                )
            )
            ->setSectionIsHidden(
                $sectionId,
                $command->getIsHidden()
            )
            ->setSectionAllowMultiple(
                $sectionId,
                $command->getAllowMultiple()
            )
            ->setSectionRepeatable(
                $sectionId,
                Repeatable::fromArray($command->getRepeatable())
            )
            ->setSectionPosition(
                $sectionId,
                $command->getPosition()
            );

        if ($command->getGroupId()) {
            $group = $this->groupRepository->findById($command->getGroupId());
            $product->setSectionGroup($sectionId, $group);
        } else {
            $product->setSectionGroup($sectionId, null);
        }

        if (null !== $command->getIsZoomable()) {
            $product->setSectionIsZoomable(
                $sectionId,
                $command->getIsZoomable()
            );
        }

        if ($command->getPreviewImage()) {
            $mediaFile = $this->getMediaFile($command->getPreviewImage());
            $product->setSectionPreviewImage(
                $sectionId,
                $mediaFile
            );
        } else {
            $product->removeSectionPreviewImage(
                $sectionId
            );
        }

        $this->productRepository->update($product);
    }

    /**
     * @param RemoveProductSection $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductSection(RemoveProductSection $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        $product->removeSection(
            new AptoUuid(
                $command->getSectionId()
            )
        );
        $this->productRepository->update($product);
    }

    /**
     * @param SetProductSectionGroup $command
     * @throws InvalidUuidException
     */
    public function handleSetProductSectionGroup(SetProductSectionGroup $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $group = $this->groupRepository->findById($command->getGroupId());
        if (null === $group) {
            return;
        }

        $product->setSectionGroup(
            new AptoUuid(
                $command->getSectionId()
            ),
            $group
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param SetProductSectionIsActive $command
     * @throws InvalidUuidException
     */
    public function handleSetProductSectionIsActive(SetProductSectionIsActive $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->setSectionIsActive(
            new AptoUuid(
                $command->getSectionId()
            ),
            $command->getIsActive()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param SetProductSectionAllowMulti $command
     * @throws InvalidUuidException
     */
    public function handleSetProductSectionAllowMulti(SetProductSectionAllowMulti $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->setSectionAllowMultiple(
            new AptoUuid(
                $command->getSectionId()
            ),
            $command->getAllowMulti()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param SetProductSectionIsMandatory $command
     * @throws InvalidUuidException
     */
    public function handleSetProductSectionIsMandatory(SetProductSectionIsMandatory $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->setSectionIsMandatory(
            new AptoUuid(
                $command->getSectionId()
            ),
            $command->getIsMandatory()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param AddProductSectionPrice $command
     * @throws InvalidUuidException
     * @throws AptoPriceDuplicateException
     */
    public function handleAddProductSectionPrice(AddProductSectionPrice $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->addSectionPrice(
            new AptoUuid(
                $command->getSectionId()
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
     * @param RemoveProductSectionPrice $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductSectionPrice(RemoveProductSectionPrice $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null !== $product) {
            $product->removeSectionPrice(
                new AptoUuid(
                    $command->getSectionId()
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
     * @param AddProductSectionDiscount $command
     * @throws AptoDiscountDuplicateException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function handleAddProductSectionDiscount(AddProductSectionDiscount $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->addSectionDiscount(
            new AptoUuid(
                $command->getSectionId()
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
     * @param RemoveProductSectionDiscount $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductSectionDiscount(RemoveProductSectionDiscount $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        $product->removeSectionDiscount(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getDiscountId()
            )
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param AddProductSectionCustomProperty $command
     * @throws InvalidUuidException
     * @throws AptoCustomPropertyException
     */
    public function handleAddProductSectionCustomProperty(AddProductSectionCustomProperty $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->addSectionCustomProperty(
            new AptoUuid(
                $command->getSectionId()
            ),
            $command->getKey(),
            $command->getValue(),
            $command->getTranslatable()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductSectionCustomProperty $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductSectionCustomProperty(RemoveProductSectionCustomProperty $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->removeSectionCustomProperty(
            new AptoUuid(
                $command->getSectionId()
            ),
            new AptoUuid(
                $command->getId()
            )
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param Product $product
     * @param Identifier $identifier
     * @param AptoUuid|null $sectionId
     * @return Identifier
     * @throws Exception
     */
    protected function findNextSectionIdentifier(Product $product, Identifier $identifier, ?AptoUuid $sectionId = null): Identifier
    {
        $identifierSearchCount = 0;

        while ($product->sectionIdentifierExists($identifier) && $identifierSearchCount < 100) {
            $identifierSearchCount++;

            if (null !== $sectionId && $this->isOwnSectionIdentifier($product, $sectionId, $identifier)) {
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
     * @param AptoUuid $sectionId
     * @param Identifier $identifier
     * @return bool
     */
    private function isOwnSectionIdentifier(Product $product, AptoUuid $sectionId, Identifier $identifier): bool
    {
        $identifierId = $product->getSectionIdByIdentifier($identifier);
        if (null !== $identifierId && $sectionId->getId() === $identifierId->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddProductSection::class => [
            'method' => 'handleAddProductSection',
            'bus' => 'command_bus'
        ];

        yield UpdateProductSection::class => [
            'method' => 'handleUpdateProductSection',
            'bus' => 'command_bus'
        ];

        yield RemoveProductSection::class => [
            'method' => 'handleRemoveProductSection',
            'bus' => 'command_bus'
        ];

        yield SetProductSectionGroup::class => [
            'method' => 'handleSetProductSectionGroup',
            'bus' => 'command_bus'
        ];

        yield SetProductSectionIsActive::class => [
            'method' => 'handleSetProductSectionIsActive',
            'bus' => 'command_bus'
        ];

        yield SetProductSectionAllowMulti::class => [
            'method' => 'handleSetProductSectionAllowMulti',
            'bus' => 'command_bus'
        ];

        yield SetProductSectionIsMandatory::class => [
            'method' => 'handleSetProductSectionIsMandatory',
            'bus' => 'command_bus'
        ];

        yield AddProductSectionPrice::class => [
            'method' => 'handleAddProductSectionPrice',
            'bus' => 'command_bus'
        ];

        yield RemoveProductSectionPrice::class => [
            'method' => 'handleRemoveProductSectionPrice',
            'bus' => 'command_bus'
        ];

        yield AddProductSectionDiscount::class => [
            'method' => 'handleAddProductSectionDiscount',
            'bus' => 'command_bus'
        ];

        yield RemoveProductSectionDiscount::class => [
            'method' => 'handleRemoveProductSectionDiscount',
            'bus' => 'command_bus'
        ];

        yield AddProductSectionCustomProperty::class => [
            'method' => 'handleAddProductSectionCustomProperty',
            'bus' => 'command_bus'
        ];

        yield RemoveProductSectionCustomProperty::class => [
            'method' => 'handleRemoveProductSectionCustomProperty',
            'bus' => 'command_bus'
        ];
    }
}
