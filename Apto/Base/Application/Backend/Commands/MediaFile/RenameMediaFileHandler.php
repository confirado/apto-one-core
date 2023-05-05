<?php

namespace Apto\Base\Application\Backend\Commands\MediaFile;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;

class RenameMediaFileHandler implements CommandHandlerInterface
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
     * @param RenameMediaFileDirectory $command
     */
    public function handleRenameDirectory(RenameMediaFileDirectory $command)
    {
        $directory = Directory::createFromPath($command->getDirectory());

        // build suitable paths for FileSystemConnector
        $pathArray = explode('/', $this->connector->getAbsolutePath($directory->getPath()));
        $pathArray[sizeof($pathArray) - 1] = $command->getNewName();
        $newPath = implode('/', $pathArray);
        $this->connector->renameDirectory($directory, $newPath);

        $files = $this->mediaFileRepository->findByDirectory($command->getDirectory());

        //build suitable paths for Repository
        $pathArray = explode('/', $directory->getPath());
        $pathArray[sizeof($pathArray) - 2] = $command->getNewName();
        $newPath = implode('/', $pathArray);

        /** @var MediaFile $mediaFile */
        foreach ($files as $mediaFile) {
            $file = $mediaFile->getFile();
            $oldFilePath = $file->getPath();
            $newFilePath = str_replace($directory->getPath(), $newPath, $oldFilePath);
            $mediaFile->setFile(File::createFromPath($newFilePath));
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield RenameMediaFileDirectory::class => [
            'method' => 'handleRenameDirectory',
            'bus' => 'command_bus'
        ];
    }
}