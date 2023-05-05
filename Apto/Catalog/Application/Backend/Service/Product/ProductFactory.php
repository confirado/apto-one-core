<?php
namespace Apto\Catalog\Application\Backend\Service\Product;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoTranslatedValueItem;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;
use Apto\Catalog\Application\Backend\Commands\Product\UpdateProduct;
use Apto\Catalog\Application\Backend\Commands\Product\AddProduct;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;

class ProductFactory
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var MediaFileRepository
     */
    protected $mediaFileRepository;

    /**
     * ProductFactory constructor.
     * @param ProductRepository $productRepository
     * @param MediaFileRepository $mediaFileRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        MediaFileRepository $mediaFileRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->mediaFileRepository = $mediaFileRepository;
    }

    /**
     * @param AddProduct $command
     * @param Identifier $identifier
     * @return Product
     * @throws \Apto\Catalog\Domain\Core\Model\Product\ProductShopCountException
     * @throws \Apto\Catalog\Domain\Core\Model\Product\ProductTaxRateException
     * @throws \Apto\Catalog\Domain\Core\Model\Product\ProductWeightException
     */
    public function createProduct(AddProduct $command, Identifier $identifier): Product
    {
        $product = new Product(
            $this->productRepository->nextIdentity(),
            $identifier,
            $this->getTranslatedValue($command->getName()),
            $this->buildShopCollection($command->getShops())
        );

        $product
            ->setDescription($this->getTranslatedValue($command->getDescription()))
            ->setCategories($this->buildCategoryCollection($command->getCategories()))
            ->setActive($command->getActive())
            ->setHidden($command->getHidden())
            ->setUseStepByStep($command->getUseStepByStep())
            ->setArticleNumber($command->getArticleNumber())
            ->setMetaTitle($this->getTranslatedValue($command->getMetaTitle()))
            ->setMetaDescription($this->getTranslatedValue($command->getMetaDescription()))
            ->setPosition($command->getPosition())
            ->setStock($command->getStock())
            ->setMaxPurchase($command->getMaxPurchase())
            ->setDeliveryTime($command->getDeliveryTime())
            ->setWeight($command->getWeight())
            ->setTaxRate($command->getTaxRate())
            ->setSeoUrl($command->getSeoUrl())
            ->setPriceCalculatorId($command->getPriceCalculatorId());

        if ($command->getPreviewImage()) {
            $mediaFile = $this->getMediaFile($command->getPreviewImage());
            $product->setPreviewImage($mediaFile);
        }

        $product ->setPosition($command->getPosition());

        return $product;
    }

    /**
     * @param UpdateProduct $command
     * @param Product $product
     * @param Identifier $identifier
     * @return Product
     * @throws \Apto\Catalog\Domain\Core\Model\Product\ProductShopCountException
     * @throws \Apto\Catalog\Domain\Core\Model\Product\ProductTaxRateException
     * @throws \Apto\Catalog\Domain\Core\Model\Product\ProductWeightException
     */
    public function updateProduct(UpdateProduct $command, Product $product, Identifier $identifier): Product
    {
        $product
            ->setIdentifier($identifier)
            ->setName($this->getTranslatedValue($command->getName()))
            ->setDescription($this->getTranslatedValue($command->getDescription()))
            ->setShops($this->buildShopCollection($command->getShops()))
            ->setCategories($this->buildCategoryCollection($command->getCategories()))
            ->setActive($command->getActive())
            ->setHidden($command->getHidden())
            ->setUseStepByStep($command->getUseStepByStep())
            ->setArticleNumber($command->getArticleNumber())
            ->setMetaTitle($this->getTranslatedValue($command->getMetaTitle()))
            ->setMetaDescription($this->getTranslatedValue($command->getMetaDescription()))
            ->setStock($command->getStock())
            ->setMaxPurchase($command->getMaxPurchase())
            ->setDeliveryTime($command->getDeliveryTime())
            ->setWeight($command->getWeight())
            ->setTaxRate($command->getTaxRate())
            ->setSeoUrl($command->getSeoUrl())
            ->setPriceCalculatorId($command->getPriceCalculatorId());

        if ($command->getPreviewImage()) {
            $mediaFile = $this->getMediaFile($command->getPreviewImage());
            $product->setPreviewImage($mediaFile);
        } else {
            $product->removePreviewImage();
        }

        $product->setPosition($command->getPosition());
        return $product;
    }

    /**
     * @param array $value
     * @return AptoTranslatedValue
     */
    protected function getTranslatedValue(array $value): AptoTranslatedValue
    {
        $translations = [];
        foreach ($value as $isoname => $translation) {
            $isocode = new AptoLocale($isoname);
            $translations[$isocode->getName()] = new AptoTranslatedValueItem(
                $isocode,
                $translation
            );
        }

        return new AptoTranslatedValue($translations);
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
}