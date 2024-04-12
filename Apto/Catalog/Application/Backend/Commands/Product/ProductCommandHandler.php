<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

use Exception;
use InvalidArgumentException;
use Money\Currency;
use Money\Money;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\AptoDiscount\AptoDiscountDuplicateException;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;

use Apto\Catalog\Application\Backend\Service\Product\ProductElementCopyProvider;
use Apto\Catalog\Application\Backend\Service\Product\ProductElementCopyRegistry;
use Apto\Catalog\Application\Backend\Service\Product\ProductPluginCopyProvider;
use Apto\Catalog\Application\Backend\Service\Product\ProductPluginCopyRegistry;
use Apto\Catalog\Domain\Core\Model\Category\CategoryRepository;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductRemoved;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Catalog\Domain\Core\Model\Shop\ShopRepository;
use Apto\Catalog\Domain\Core\Model\Filter\FilterPropertyRepository;
use Apto\Catalog\Domain\Core\Model\Product\DomainProperties;
use Apto\Catalog\Domain\Core\Model\Product\DomainPropertiesRepository;
use Apto\Catalog\Domain\Core\Model\Shop\Shop;
use Apto\Catalog\Domain\Core\Model\Product\ProductShopCountException;
use Apto\Catalog\Domain\Core\Model\Product\ProductTaxRateException;
use Apto\Catalog\Domain\Core\Model\Product\ProductWeightException;


class ProductCommandHandler extends ProductChildHandler
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var DomainPropertiesRepository
     */
    protected $domainPropertiesRepository;

    /**
     * @var MediaFileRepository
     */
    protected $mediaFileRepository;

    /**
     * @var MediaFileSystemConnector
     */
    protected $fileSystemConnector;

    /**
     * @var ProductElementCopyRegistry
     */
    protected $productElementCopyRegistry;

    /**
     * @var ProductPluginCopyRegistry
     */
    protected $productPluginCopyRegistry;

    /**
     * @var FilterPropertyRepository
     */
    protected $filterPropertyRepository;

    /**
     * ProductCommandHandler constructor.
     * @param ProductRepository $productRepository
     * @param ShopRepository $shopRepository
     * @param CategoryRepository $categoryRepository
     * @param MediaFileRepository $mediaFileRepository
     * @param MediaFileSystemConnector $fileSystemConnector
     * @param ProductElementCopyRegistry $productElementCopyRegistry
     * @param ProductPluginCopyRegistry $productPluginCopyRegistry
     * @param FilterPropertyRepository $filterPropertyRepository
     * @param DomainPropertiesRepository $domainPropertiesRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ShopRepository $shopRepository,
        CategoryRepository $categoryRepository,
        MediaFileRepository $mediaFileRepository,
        MediaFileSystemConnector $fileSystemConnector,
        ProductElementCopyRegistry $productElementCopyRegistry,
        ProductPluginCopyRegistry $productPluginCopyRegistry,
        FilterPropertyRepository $filterPropertyRepository,
        DomainPropertiesRepository $domainPropertiesRepository
    ) {
        parent::__construct($productRepository);
        $this->shopRepository = $shopRepository;
        $this->categoryRepository = $categoryRepository;
        $this->mediaFileRepository = $mediaFileRepository;
        $this->fileSystemConnector = $fileSystemConnector;
        $this->productElementCopyRegistry = $productElementCopyRegistry;
        $this->productPluginCopyRegistry = $productPluginCopyRegistry;
        $this->filterPropertyRepository = $filterPropertyRepository;
        $this->domainPropertiesRepository = $domainPropertiesRepository;
    }

    /**
     * @param AddProduct $command
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     * @throws ProductIdentifierAlreadyExists
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleAddProduct(AddProduct $command)
    {
        $productName = $this->getTranslatedValue($command->getName());
        $newIdentifier = $this->getIdentifier($command->getIdentifier(), $productName);

        if (null === $newIdentifier) {
            throw new InvalidArgumentException('Could not generate any Identifier because Identifier and Name are empty!');
        }

        $newIdentifier = $this->findNextIdentifier($newIdentifier);
        $shops = $this->buildShopCollection($command->getShops());

        $product = new Product(
            $this->productRepository->nextIdentity(),
            $newIdentifier,
            $productName,
            $shops
        );

        $product
            ->setDescription($this->getTranslatedValue($command->getDescription()))
            ->setCategories($this->buildCategoryCollection($command->getCategories()))
            ->setDomainProperties($this->buildNewDomainPropertiesCollection($product, $shops))
            ->setActive($command->getActive())
            ->setHidden($command->getHidden())
            ->setUseStepByStep($command->getUseStepByStep())
            ->setArticleNumber($command->getArticleNumber())
            ->setMetaTitle($this->getTranslatedValue($command->getMetaTitle()))
            ->setMetaDescription($this->getTranslatedValue($command->getMetaDescription()))
            ->setStock($command->getStock())
            ->setDeliveryTime($command->getDeliveryTime())
            ->setWeight($command->getWeight())
            ->setTaxRate($command->getTaxRate())
            ->setSeoUrl($command->getSeoUrl())
            ->setPriceCalculatorId($command->getPriceCalculatorId());

        // reset min and max purchase to prevent error caused by the validation
        $product
            ->setMinPurchase(0)
            ->setMaxPurchase(0);

        // set max before min to prevent error caused by the validation
        $product
            ->setMaxPurchase($command->getMaxPurchase())
            ->setMinPurchase($command->getMinPurchase());

        if ($command->getPreviewImage()) {
            $mediaFile = $this->getMediaFile($command->getPreviewImage());
            $product->setPreviewImage($mediaFile);
        }

        $product->setPosition($command->getPosition());
        $product->setKeepSectionOrder($command->getKeepSectionOrder());
        $this->productRepository->add($product);

        $product->publishEvents();
    }

    /**
     * @param UpdateProduct $command
     * @throws ProductIdentifierAlreadyExists
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     * @throws Exception
     */
    public function handleUpdateProduct(UpdateProduct $command)
    {
        $product = $this->productRepository->findById($command->getId());

        if (null !== $product) {
            $productName = $this->getTranslatedValue($command->getName());
            $newIdentifier = $this->getIdentifier($command->getIdentifier(), $productName);

            if (null === $newIdentifier) {
                throw new InvalidArgumentException('Could not generate any Identifier because Identifier and Name are empty!');
            }

            $newIdentifier = $this->findNextIdentifier($newIdentifier, $command->getId());

            $product
                ->setIdentifier($newIdentifier)
                ->setName($productName)
                ->setDescription($this->getTranslatedValue($command->getDescription()))
                ->setShops($this->buildShopCollection($command->getShops()))
                ->setCategories($this->buildCategoryCollection($command->getCategories()))
                ->setDomainProperties($this->buildDomainPropertiesCollection($command->getDomainProperties(), $command->getShops(), $product))
                ->setActive($command->getActive())
                ->setHidden($command->getHidden())
                ->setUseStepByStep($command->getUseStepByStep())
                ->setArticleNumber($command->getArticleNumber())
                ->setMetaTitle($this->getTranslatedValue($command->getMetaTitle()))
                ->setMetaDescription($this->getTranslatedValue($command->getMetaDescription()))
                ->setStock($command->getStock())
                ->setDeliveryTime($command->getDeliveryTime())
                ->setWeight($command->getWeight())
                ->setTaxRate($command->getTaxRate())
                ->setSeoUrl($command->getSeoUrl())
                ->setPriceCalculatorId($command->getPriceCalculatorId());

            // reset min and max purchase to prevent error caused by the validation
            $product
                ->setMinPurchase(0)
                ->setMaxPurchase(0);

            // set max before min to prevent error caused by the validation
            $product
                ->setMaxPurchase($command->getMaxPurchase())
                ->setMinPurchase($command->getMinPurchase());

            if ($command->getPreviewImage()) {
                $mediaFile = $this->getMediaFile($command->getPreviewImage());
                $product->setPreviewImage($mediaFile);
            } else {
                $product->removePreviewImage();
            }

            $product ->setPosition($command->getPosition());
            $product ->setKeepSectionOrder($command->getKeepSectionOrder());

            $filterProperties = new ArrayCollection();
            foreach ($command->getFilterPropertyIds() as $property) {
                $filterProperty = $this->filterPropertyRepository->findById($property['id']);
                if ($filterProperty !== null) {
                    $filterProperties->add($filterProperty);
                }
            }

            $this->productRepository->update($product);
            $product->publishEvents();
        }
    }

    /**
     * @param CopyProduct $command
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     * @throws ProductShopCountException
     */
    public function handleCopyProduct(CopyProduct $command)
    {
        $product = $this->productRepository->findById($command->getId());

        if (null !== $product) {
            $copiedProduct = $product->copy(
                $this->productRepository->nextIdentity()
            );

            $this->productRepository->add($copiedProduct);
            $this->productRepository->flush($copiedProduct);

            $sectionIds = $copiedProduct->getSectionIds();

            $entityMapping = [];
            $entityMapping[$product->getId()->getId()] = $copiedProduct->getId()->getId();

            foreach ($sectionIds as $sectionId) {
                $sectionId = new AptoUuid($sectionId);
                $productSectionsElements = $this->productRepository->findSectionsElementsAsArray($product->getId()->getId());
                $oldSectionId = $this->findSectionByIdentifier(
                    $copiedProduct->getSectionIdentifier(
                        $sectionId
                    )->getValue(),
                    $productSectionsElements
                );
                $entityMapping[$oldSectionId->getId()] = $sectionId->getId();
                $elementIds = $copiedProduct->getElementIds($sectionId);
                foreach ($elementIds as $elementId) {
                    $elementId = new AptoUuid($elementId);
                    $definition = get_class($copiedProduct->getElementDefinition($sectionId, $elementId));
                    $oldElementId = $this->findElementByIdentifier(
                        $copiedProduct->getElementIdentifier($sectionId, $elementId)->getValue(),
                        $productSectionsElements
                    );
                    $entityMapping[$oldElementId->getId()] = $elementId->getId();
                    $elementCopyProvider = $this->getElementCopyProvider($definition);

                    if ($elementCopyProvider) {
                        $elementCopyProvider->copy(
                            $oldElementId,
                            $copiedProduct->getId(),
                            $sectionId,
                            $elementId
                        );
                    }
                }
            }

            /** @var ProductPluginCopyProvider $productPluginCopyProvider */
            foreach ($this->productPluginCopyRegistry->getProductPluginCopyProviders() as $productPluginCopyProvider) {
                $productPluginCopyProvider->copy($product->getId(), $copiedProduct->getId(), $entityMapping);
            }

            $copiedProduct->publishEvents();
        }
    }

    /**
     * @param CopyProductSection $command
     * @throws InvalidUuidException
     */
    public function handleCopyProductSection(CopyProductSection $command)
    {
        $product = $this->productRepository->findById($command->getId());

        if (null !== $product) {
            $product = $product->copySection(
                new AptoUuid($command->getSectionId())
            );

            $this->productRepository->add($product);
            $this->productRepository->flush($product);

            $product->publishEvents();
        }
    }

    /**
     * @param CopyProductElement $command
     * @throws InvalidUuidException
     */
    public function handleCopyProductElement(CopyProductElement $command)
    {
        $product = $this->productRepository->findById($command->getId());

        if (null !== $product) {
            $product->copyElement(
                new AptoUuid($command->getSectionId()),
                new AptoUuid($command->getElementId())
            );

            $this->productRepository->add($product);
            $this->productRepository->flush($product);

            $product->publishEvents();
        }
    }

    /**
     * @param RemoveProduct $command
     */
    public function handleRemoveProduct(RemoveProduct $command)
    {
        $product = $this->productRepository->findById($command->getId());

        if (null !== $product) {
            $this->productRepository->remove($product);
            DomainEventPublisher::instance()->publish(
                new ProductRemoved(
                    $product->getId()
                )
            );
        }
    }

    /**
     * @param AddProductPrice $command
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function handleAddProductPrice(AddProductPrice $command)
    {
        $product = $this->productRepository->findById($command->getId());
        if (null !== $product) {
            $product->addAptoPrice(
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
    }

    /**
     * @param UpdateProductPrice $command
     * @throws InvalidUuidException
     * @throws AptoPriceDuplicateException
     */
    public function handleUpdateProductPrice(UpdateProductPrice $command)
    {
        $product = $this->productRepository->findById($command->getId());
        if (null !== $product) {
            $priceId = new AptoUuid($command->getPriceId());
            $product
                ->setAptoPricePrice(
                    $priceId,
                    new Money(
                        $command->getAmount(),
                        new Currency(
                            $command->getCurrency()
                        )
                    )
                )
                ->setAptoPriceCustomerGroupId(
                    $priceId,
                    new AptoUuid(
                        $command->getCustomerGroupId()
                    )
                );
            $this->productRepository->update($product);
            $product->publishEvents();
        }
    }

    /**
     * @param RemoveProductPrice $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductPrice(RemoveProductPrice $command)
    {
        $product = $this->productRepository->findById($command->getId());
        if (null !== $product) {
            $product->removeAptoPrice(
                new AptoUuid(
                    $command->getPriceId()
                )
            );
            $this->productRepository->update($product);
            $product->publishEvents();
        }
    }

    /**
     * @param AddProductDiscount $command
     * @throws InvalidUuidException
     * @throws AptoDiscountDuplicateException
     * @throws InvalidTranslatedValueException
     */
    public function handleAddProductDiscount(AddProductDiscount $command)
    {
        $product = $this->productRepository->findById($command->getId());

        if (null === $product) {
            return;
        }

        $product->addAptoDiscount(
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
     * @param RemoveProductDiscount $command
     * @throws InvalidUuidException
     */
    public function handleRemoveProductDiscount(RemoveProductDiscount $command)
    {
        $product = $this->productRepository->findById($command->getId());

        if (null === $product) {
            return;
        }

        $product->removeAptoDiscount(
            new AptoUuid(
                $command->getDiscountId()
            )
        );
        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param AddProductCustomProperty $command
     */
    public function handleAddProductCustomProperty(AddProductCustomProperty $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->setCustomProperty(
            $command->getKey(),
            $command->getValue(),
            $command->getTranslatable()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveProductCustomProperty $command
     */
    public function handleRemoveProductCustomProperty(RemoveProductCustomProperty $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $product) {
            return;
        }

        $product->removeCustomProperty(
            $command->getKey()
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param Identifier $identifier
     * @param string|null $id
     * @param int $currentIteration
     * @return Identifier
     * @throws ProductIdentifierAlreadyExists
     */
    protected function findNextIdentifier(Identifier $identifier, ?string $id = null, int $currentIteration = 0): Identifier
    {
        try {
            $this->checkUniqueConstraints($identifier, $id);
            return $identifier;
        } catch (ProductIdentifierAlreadyExists $exception) {
            $currentIteration++;

            if ($currentIteration > 100) {
                throw $exception;
            }

            if ($currentIteration === 1) {
                $newIdentifier = new Identifier($identifier->getValue() . '-' . $currentIteration);
                return $this->findNextIdentifier($newIdentifier, $id, $currentIteration);
            }

            $previousIdentifierValue = $identifier->getValue();
            $orgIdentifierLength = strrpos($previousIdentifierValue, '-');
            $newIdentifierValue = substr($previousIdentifierValue, 0, $orgIdentifierLength + 1) . $currentIteration;

            return $this->findNextIdentifier(new Identifier($newIdentifierValue), $id, $currentIteration);
        }
    }

    /**
     * @param array $shops
     * @return ArrayCollection
     */
    protected function buildShopCollection(array $shops)
    {
        $shopCollection = new ArrayCollection();
        foreach ($shops as $shop) {
            $shopModel = $this->shopRepository->findById($shop['id']);
            $shopCollection->add($shopModel);
        }

        return $shopCollection;
    }


    /**
     * @param array $domainPropertiesArray
     * @param array $shops
     * @param Product $product
     * @return ArrayCollection
     * @throws InvalidUuidException
     */
    protected function buildDomainPropertiesCollection(array $domainPropertiesArray, array $shops, Product $product): ArrayCollection
    {
        $newDomainProperties = new ArrayCollection();
        $remainingDomainProperties = [];

        // remove old DomainProperties no longer used
        foreach ($domainPropertiesArray as $domainProperties) {
            $delete = true;
            foreach ($shops as $shop) {
                if ($domainProperties['shop']['id'] === $shop['id']) {
                    $delete = false;
                }
            }
            if ($delete) {
                $domainPropertiesObject = $this->domainPropertiesRepository->findById($domainProperties['id']);
                $this->domainPropertiesRepository->remove($domainPropertiesObject);
            } else {
                $remainingDomainProperties[] = $domainProperties;
            }
        }

        // add new DomainProperties
        foreach ($shops as $shop) {
            $notPresent = true;
            foreach ($domainPropertiesArray as $domainProperties) {
                if ($domainProperties['shop']['id'] === $shop['id']) {
                    $notPresent = false;
                }
            }
            if ($notPresent) {
                $newDomainProperties->add($this->makeNewDomainProperties($product, $this->shopRepository->findById($shop['id'])));
            }
        }

        // update DomainProperties
        foreach ($remainingDomainProperties as $domainProperties) {
            $domainPropertiesObject = $this->domainPropertiesRepository->findById($domainProperties['id']);
            $domainPropertiesObject->setPriceModifier((float) str_replace(',', ',', $domainProperties['priceModifier']));

            if ($domainProperties['previewImage']) {
                $mediaFile = $this->getMediaFile($domainProperties['previewImage']);
                $domainPropertiesObject->setPreviewImage($mediaFile);
            } else {
                $domainPropertiesObject->setPreviewImage();
            }

            $this->domainPropertiesRepository->update($domainPropertiesObject);
            $newDomainProperties->add($domainPropertiesObject);
        }
        $this->domainPropertiesRepository->flush();
        return new $newDomainProperties;
    }

    /**
     * @param Product $product
     * @param Collection $shops
     * @return ArrayCollection
     */
    protected function buildNewDomainPropertiesCollection(Product $product, Collection $shops): ArrayCollection
    {
        $domainPropertiesCollection = new ArrayCollection();
        /** @var Shop $shop */
        foreach ($shops as $shop) {
            $domainProperties = $this->makeNewDomainProperties($product, $shop);
            $domainPropertiesCollection->add($domainProperties);
        }
        return $domainPropertiesCollection;
    }

    /**
     * @param Product $product
     * @param Shop $shop
     * @return DomainProperties
     */
    protected function makeNewDomainProperties(Product $product, Shop $shop): DomainProperties
    {
        $domainProperties = new DomainProperties($product, $shop);
        $this->domainPropertiesRepository->add($domainProperties);
        return $domainProperties;
    }

    /**
     * @param array $categories
     * @return ArrayCollection
     */
    protected function buildCategoryCollection(array $categories)
    {
        $categoryCollection = new ArrayCollection();
        foreach ($categories as $category) {
            $categoryModel = $this->categoryRepository->findById($category['id']);
            $categoryCollection->add($categoryModel);
        }

        return $categoryCollection;
    }

    /**
     * @param Identifier $identifier
     * @param string|null $id
     * @return void
     * @throws ProductIdentifierAlreadyExists
     */
    protected function checkUniqueConstraints(Identifier $identifier, ?string $id = null)
    {
        $productAlreadyExists = $this->productRepository->findByIdentifier($identifier);

        if (null !== $productAlreadyExists) {
            if (null === $id) {
                throw new ProductIdentifierAlreadyExists('Product Identifier already set on Product width Id: ' . $productAlreadyExists->getId()->getId() . '.');
            }

            if ($productAlreadyExists->getId()->getId() !== $id) {
                throw new ProductIdentifierAlreadyExists('Product Identifier already set on Product width Id: ' . $productAlreadyExists->getId()->getId() . '.');
            }
        }
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
     * @param string $type
     * @return false|ProductElementCopyProvider
     */
    private function getElementCopyProvider(string $type)
    {
        $productElementCopyProviders = $this->productElementCopyRegistry->getProductElementCopyProviders();
        foreach ($productElementCopyProviders as $productElementCopyProvider) {
            if ($productElementCopyProvider->getType() === $type) {
                return $productElementCopyProvider;
            }
        }
        return false;
    }

    /**
     * @param string $identifier
     * @param array $product
     * @return AptoUuid
     * @throws InvalidUuidException
     */
    private function findElementByIdentifier(string $identifier, array $product): AptoUuid
    {
        foreach ($product['sections'] as $section) {
            foreach ($section['elements'] as $element) {
                if ($element['identifier'] === $identifier) {
                    return new AptoUuid($element['id']);
                }
            }
        }
        throw new \InvalidArgumentException('Identifier : "' . $identifier . '" can not found.');
    }

    /**
     * @param string $identifier
     * @param array $product
     * @return AptoUuid
     * @throws InvalidUuidException
     */
    private function findSectionByIdentifier(string $identifier, array $product): AptoUuid
    {
        foreach ($product['sections'] as $section) {
            if ($section['identifier'] === $identifier) {
                return new AptoUuid($section['id']);
            }
        }
        throw new \InvalidArgumentException('Identifier : "' . $identifier . '" can not found.');
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddProduct::class => [
            'method' => 'handleAddProduct',
            'bus' => 'command_bus'
        ];

        yield UpdateProduct::class => [
            'method' => 'handleUpdateProduct',
            'bus' => 'command_bus'
        ];

        yield CopyProduct::class => [
            'method' => 'handleCopyProduct',
            'bus' => 'command_bus'
        ];

        yield CopyProductSection::class => [
            'method' => 'handleCopyProductSection',
            'bus' => 'command_bus'
        ];

        yield CopyProductElement::class => [
            'method' => 'handleCopyProductElement',
            'bus' => 'command_bus'
        ];

        yield RemoveProduct::class => [
            'method' => 'handleRemoveProduct',
            'bus' => 'command_bus'
        ];

        yield AddProductPrice::class => [
            'method' => 'handleAddProductPrice',
            'bus' => 'command_bus'
        ];

        yield UpdateProductPrice::class => [
            'method' => 'handleUpdateProductPrice',
            'bus' => 'command_bus'
        ];

        yield RemoveProductPrice::class => [
            'method' => 'handleRemoveProductPrice',
            'bus' => 'command_bus'
        ];

        yield AddProductDiscount::class => [
            'method' => 'handleAddProductDiscount',
            'bus' => 'command_bus'
        ];

        yield RemoveProductDiscount::class => [
            'method' => 'handleRemoveProductDiscount',
            'bus' => 'command_bus'
        ];

        yield AddProductCustomProperty::class => [
            'method' => 'handleAddProductCustomProperty',
            'bus' => 'command_bus'
        ];

        yield RemoveProductCustomProperty::class => [
            'method' => 'handleRemoveProductCustomProperty',
            'bus' => 'command_bus'
        ];
    }
}
