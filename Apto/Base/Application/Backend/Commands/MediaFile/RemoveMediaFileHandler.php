<?php

namespace Apto\Base\Application\Backend\Commands\MediaFile;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotRemovableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotRemovableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemMountedReadOnlyException;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRemoved;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;

class RemoveMediaFileHandler implements CommandHandlerInterface
{

    /**
     * @var MediaFileRepository
     */
    protected $mediaFileRepository;

    /**
     * @var MediaFileSystemConnector
     */
    protected $connector;

    /**
     * AddMediaFileHandler constructor.
     * @param MediaFileRepository $mediaFileRepository
     * @param MediaFileSystemConnector $connector
     */
    public function __construct(MediaFileRepository $mediaFileRepository, MediaFileSystemConnector $connector)
    {
        $this->mediaFileRepository = $mediaFileRepository;
        $this->connector = $connector;
    }

    /**
     * @param RemoveMediaFile $command
     * @throws FileNotRemovableException
     * @throws FileSystemMountedReadOnlyException
     */
    public function handleRemoveById(RemoveMediaFile $command)
    {
        $mediaFile = $this->mediaFileRepository->findById($command->getId());
        if (null !== $mediaFile) {
            $file = $mediaFile->getFile();
            $this->mediaFileRepository->remove($mediaFile);
            DomainEventPublisher::instance()->publish(
                new MediaFileRemoved(
                    $mediaFile->getId()
                )
            );
            $this->connector->removeFile($file);
        }
    }

    /**
     * @param RemoveMediaFileByName $command
     * @throws FileNotRemovableException
     * @throws FileSystemMountedReadOnlyException
     */
    public function handleRemoveByName(RemoveMediaFileByName $command)
    {
        $file = File::createFromPath($command->getFile());
        $this->removeMediaFileByFile($file);
    }

    /**
     * @param RemoveMediaFileDirectory $command
     * @throws DirectoryNotRemovableException
     * @throws FileSystemMountedReadOnlyException
     */
    public function handleRemoveDirectory(RemoveMediaFileDirectory $command)
    {
        $directory = Directory::createFromPath($command->getDirectory());
        $this->connector->removeDirectory($directory);
    }

    /**
     * @param File $file
     * @throws FileNotRemovableException
     * @throws FileSystemMountedReadOnlyException
     */
    private function removeMediaFileByFile(File $file)
    {
        $mediaFile = $this->mediaFileRepository->findOneByFile($file);

        if (null !== $mediaFile) {
            // remove model, file will be deleted by postDelete listener
            $this->mediaFileRepository->remove($mediaFile);
            DomainEventPublisher::instance()->publish(
                new MediaFileRemoved(
                    $mediaFile->getId()
                )
            );
        } else {
            // no model, remove file directly
            if ($this->connector->existsFile($file)) {
                $this->connector->removeFile($file);
            }
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield RemoveMediaFile::class => [
            'method' => 'handleRemoveById',
            'bus' => 'command_bus'
        ];

        yield RemoveMediaFileByName::class => [
            'method' => 'handleRemoveByName',
            'bus' => 'command_bus'
        ];

        yield RemoveMediaFileDirectory::class => [
            'method' => 'handleRemoveDirectory',
            'bus' => 'command_bus'
        ];
    }
}