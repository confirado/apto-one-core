<?php
namespace Apto\Catalog\Application\Backend\Commands\Category;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;
use Apto\Catalog\Domain\Core\Model\Category\Category;
use Apto\Catalog\Domain\Core\Model\Category\CategoryParentException;
use Apto\Catalog\Domain\Core\Model\Category\CategoryRemoved;
use Apto\Catalog\Domain\Core\Model\Category\CategoryRepository;

class CategoryCommandHandler extends AbstractCommandHandler
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var MediaFileRepository
     */
    protected $mediaFileRepository;

    /**
     * @var MediaFileSystemConnector
     */
    protected $fileSystemConnector;

    /**
     * CategoryCommandHandler constructor.
     * @param CategoryRepository $categoryRepository
     * @param MediaFileRepository $mediaFileRepository
     * @param MediaFileSystemConnector $fileSystemConnector
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        MediaFileRepository $mediaFileRepository,
        MediaFileSystemConnector $fileSystemConnector
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->mediaFileRepository = $mediaFileRepository;
        $this->fileSystemConnector = $fileSystemConnector;
    }

    /**
     * @param AddCategory $command
     * @throws InvalidUuidException
     * @throws CategoryParentException
     */
    public function handleAddCategory(AddCategory  $command)
    {
        $parent = $this->categoryRepository->findById($command->getParent());
        $category = new Category(
            $this->categoryRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName()),
            $command->getPosition(),
            $parent
        );

        $category->setDescription($this->getTranslatedValue($command->getDescription()));
        if ($command->getPreviewImage()) {
            $mediaFile = $this->getMediaFile($command->getPreviewImage());
            $category->setPreviewImage($mediaFile);
        }

        $this->categoryRepository->add($category);
        $category->publishEvents();
    }

    /**
     * @param AddCategoryCustomProperty $command
     * @throws AptoCustomPropertyException
     */
    public function handleAddCategoryCustomProperty(AddCategoryCustomProperty $command)
    {
        $category = $this->categoryRepository->findById($command->getCategoryId());

        if (null === $category) {
            return;
        }

        $category->setCustomProperty(
            $command->getKey(),
            $command->getValue(),
            $command->getTranslatable()
        );

        $this->categoryRepository->update($category);
        $category->publishEvents();
    }

    /**
     * @param RemoveCategoryCustomProperty $command
     */
    public function handleRemoveCategoryCustomProperty(RemoveCategoryCustomProperty $command)
    {
        $category = $this->categoryRepository->findById($command->getCategoryId());

        if (null === $category) {
            return;
        }

        $category->removeCustomProperty(
            new AptoUuid($command->getId())
        );

        $this->categoryRepository->update($category);
        $category->publishEvents();
    }

    public function handleRemoveCategory(RemoveCategory $command)
    {
        $category = $this->categoryRepository->findById($command->getId());

        if(null !== $category) {
            $this->categoryRepository->remove($category);
            DomainEventPublisher::instance()->publish(
                new CategoryRemoved(
                    $category->getId()
                )
            );
        }
    }

    /**
     * @param UpdateCategory $command
     * @throws CategoryParentException
     * @throws InvalidUuidException
     */
    public function handleUpdateCategory(UpdateCategory $command)
    {
        $category = $this->categoryRepository->findById($command->getId());

        if (null !== $category) {
            $category
                ->setName(
                    $this->getTranslatedValue($command->getName())
                )
                ->setDescription(
                    $this->getTranslatedValue($command->getDescription())
                )
                ->setParent(
                    $this->categoryRepository->findById($command->getParent())
                )
                ->setPosition(
                    $command->getPosition()
                )
            ;
            if ($command->getPreviewImage()) {
                $mediaFile = $this->getMediaFile($command->getPreviewImage());
                $category->setPreviewImage($mediaFile);
            } else {
                $category->removePreviewImage();
            }

            $this->categoryRepository->update($category);
            $category->publishEvents();
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
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddCategory::class => [
            'method' => 'handleAddCategory',
            'bus' => 'command_bus'
        ];

        yield AddCategoryCustomProperty::class => [
            'method' => 'handleAddCategoryCustomProperty',
            'bus' => 'command_bus'
        ];

        yield RemoveCategoryCustomProperty::class => [
            'method' => 'handleRemoveCategoryCustomProperty',
            'bus' => 'command_bus'
        ];

        yield RemoveCategory::class => [
            'method' => 'handleRemoveCategory',
            'bus' => 'command_bus'
        ];

        yield UpdateCategory::class => [
            'method' => 'handleUpdateCategory',
            'bus' => 'command_bus'
        ];
    }
}
