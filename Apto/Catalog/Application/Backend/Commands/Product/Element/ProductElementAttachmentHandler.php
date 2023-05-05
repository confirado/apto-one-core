<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;
use Apto\Catalog\Application\Backend\Commands\Product\ProductChildHandler;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

class ProductElementAttachmentHandler extends ProductChildHandler
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
     * @param AddProductElementAttachment $command
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function handleAddProductElementAttachment(AddProductElementAttachment $command)
    {
        $attachment = $command->getAttachment();

        if(!array_key_exists('file', $attachment) || !array_key_exists('name', $attachment)) {
            return;
        }
        $mediaFile = $this->getMediaFile($attachment['file']);

        $product = $this->productRepository->findById($command->getProductId());

        $sectionId = new AptoUuid($command->getSectionId());
        $elementId = new AptoUuid($command->getElementId());

        $product->addElementAttachment(
            $sectionId,
            $elementId,
            $this->getTranslatedValue($attachment['name']),
            $mediaFile
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param string $path
     * @return MediaFile
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
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
        yield AddProductElementAttachment::class => [
            'method' => 'handleAddProductElementAttachment',
            'bus' => 'command_bus'
        ];
    }
}
