<?php

namespace Apto\Base\Application\Backend\Commands\MediaFile;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileAlreadyExistsException;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;

class AddMediaFileHandler implements CommandHandlerInterface
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
     * @param AddMediaFile $command
     * @throws MediaFileAlreadyExistsException
     */
    public function handleAddFile(AddMediaFile $command)
    {
        // @todo maybe we should build a MediaFileFactory here to make it more reusable for other commands that want to add a reference to a media file
        $file = File::createFromPath($command->getFile());

        $existingMediaFile = $this->mediaFileRepository->findOneByFile($file);
        if (null !== $existingMediaFile) {
            throw new MediaFileAlreadyExistsException($command->getFile());
        }

        $mediaFile = new MediaFile(
            $this->mediaFileRepository->nextIdentity(),
            $file
        );
        $mediaFile
            ->setSize($this->connector->getFileSize($file))
            ->setMd5Hash($this->connector->getFileMd5Hash($file));

        $this->mediaFileRepository->add($mediaFile);
        $mediaFile->publishEvents();
    }

    /**
     * @param AddMediaFileDirectory $command
     */
    public function handleAddDirectory(AddMediaFileDirectory $command)
    {
        $directory = Directory::createFromPath($command->getDirectory());
        $this->connector->createDirectory($directory);
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddMediaFile::class => [
            'method' => 'handleAddFile',
            'bus' => 'command_bus'
        ];

        yield AddMediaFileDirectory::class => [
            'method' => 'handleAddDirectory',
            'bus' => 'command_bus'
        ];
    }
}