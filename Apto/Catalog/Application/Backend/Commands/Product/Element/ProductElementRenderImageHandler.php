<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;
use Apto\Catalog\Application\Backend\Commands\Product\ProductChildHandler;
use Apto\Catalog\Domain\Core\Model\Product\Element\RenderImageOptions;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

class ProductElementRenderImageHandler extends ProductChildHandler
{
    /**
     * @var MediaFileRepository
     */
    protected $mediaFileRepository;

    /**
     * @var MediaFileSystemConnector
     */
    protected $fileSystemConnector;

    /**
     * AddProductElementRenderImageHandler constructor.
     * @param ProductRepository $productRepository
     * @param MediaFileRepository $mediaFileRepository
     * @param MediaFileSystemConnector $fileSystemConnector
     */
    public function __construct(ProductRepository $productRepository, MediaFileRepository $mediaFileRepository, MediaFileSystemConnector $fileSystemConnector)
    {
        parent::__construct($productRepository);
        $this->mediaFileRepository = $mediaFileRepository;
        $this->fileSystemConnector = $fileSystemConnector;
    }

    /**
     * @param AddProductElementRenderImage $command
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function handleAddProductElementRenderImage(AddProductElementRenderImage $command)
    {
        $renderImageOptions = new RenderImageOptions($command->getRenderImageOptions(), $command->getOffsetOptions());

        $mediaFile = $this->getMediaFile($renderImageOptions->getFile());

        $product = $this->productRepository->findById($command->getProductId());

        $sectionId = new AptoUuid($command->getSectionId());
        $elementId = new AptoUuid($command->getElementId());

        $product->addElementRenderImage(
            $sectionId,
            $elementId,
            (int) $renderImageOptions->getLayer(),
            $renderImageOptions->getPerspective(),
            $mediaFile,
            $renderImageOptions->getOffsetX(),
            $renderImageOptions->getOffsetUnitX(),
            $renderImageOptions->getOffsetY(),
            $renderImageOptions->getOffsetUnitY(),
            $renderImageOptions

        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param string $path
     * @return MediaFile
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
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddProductElementRenderImage::class => [
            'method' => 'handleAddProductElementRenderImage',
            'bus' => 'command_bus'
        ];
    }
}
