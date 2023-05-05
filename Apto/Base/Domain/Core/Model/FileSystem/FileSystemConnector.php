<?php

namespace Apto\Base\Domain\Core\Model\FileSystem;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemMountedReadOnlyException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotRemovableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryPermissionSetFailedException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotRemovableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FilePermissionSetFailedException;

interface FileSystemConnector
{
    /**
     * @return bool
     */
    public function isReadOnly(): bool;

    /**
     * @param string $path
     * @return string
     */
    public function getAbsolutePath(string $path): string;

    /**
     * @param File $file
     * @return bool
     */
    public function existsFile(File $file): bool;

    /**
     * @param Directory $directory
     * @return bool
     */
    public function existsDirectory(Directory $directory): bool;

    /**
     * @param File $file
     * @return bool
     */
    public function isFileEmpty(File $file): bool;

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryEmpty(Directory $directory): bool;

    /**
     * @param File $file
     * @return bool
     */
    public function isFileReadable(File $file): bool;

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryReadable(Directory $directory): bool;

    /**
     * @param File $file
     * @return bool
     */
    public function isFileWritable(File $file): bool;

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryWritable(Directory $directory): bool;

    /**
     * @param File $file
     * @param File|null $source
     * @param FileSystemConnector $sourceConnector
     * @param bool $overwriteExisting
     * @return bool
     */
    public function isFileCreatable(File $file, File $source = null, FileSystemConnector $sourceConnector, bool $overwriteExisting = false): bool;

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryCreatable(Directory $directory): bool;

    /**
     * @param File $file
     * @param File|null $source
     * @param FileSystemConnector|null $sourceConnector
     * @param bool $overwriteExisting
     * @return FileSystemConnector
     * @throws FileNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     */
    public function createFile(File $file, File $source = null, FileSystemConnector $sourceConnector = null, bool $overwriteExisting = false): FileSystemConnector;

    /**
     * @param Directory $directory
     * @param bool $recursive
     * @return FileSystemConnector
     * @throws DirectoryNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     */
    public function createDirectory(Directory $directory, bool $recursive = false): FileSystemConnector;

    /**
     * @param Directory $directory
     * @param string $newPath
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     */
    public function renameDirectory(Directory $directory, string $newPath): FileSystemConnector;

    /**
     * @param File $file
     * @return bool
     */
    public function isFileRemovable(File $file): bool;

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryRemovable(Directory $directory): bool;

    /**
     * @param File $file
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     * @throws FileNotRemovableException
     */
    public function removeFile(File $file): FileSystemConnector;

    /**
     * @param Directory $directory
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     * @throws DirectoryNotRemovableException
     */
    public function removeDirectory(Directory $directory): FileSystemConnector;

    /**
     * @param Directory $directory
     *
     * @return void
     */
    public function clearDirectory(Directory $directory): void;

    /**
     * @param File $file
     * @return int
     */
    public function getFilePermission(File $file): int;

    /**
     * @param Directory $directory
     * @return int
     */
    public function getDirectoryPermission(Directory $directory): int;

    /**
     * @param File $file
     * @param int $permission
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     * @throws FilePermissionSetFailedException
     */
    public function setFilePermission(File $file, int $permission = null): FileSystemConnector;

    /**
     * @param Directory $directory
     * @param int $permission
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     * @throws DirectoryPermissionSetFailedException
     */
    public function setDirectoryPermission(Directory $directory, int $permission = null): FileSystemConnector;

    /**
     * @param File $file
     * @return string
     */
    public function getFileContent(File $file): string;

    /**
     * @param Directory $directory
     * @param array $allowedExtensions
     * @return array
     */
    public function getDirectoryContent(Directory $directory, array $allowedExtensions = []): array;

    /**
     * @param File $file
     * @return int
     */
    public function getFileSize(File $file): int;

    /**
     * @param File $file
     * @return string
     */
    public function getFileMd5Hash(File $file): string;

    /**
     * @param File $file
     * @return string
     */
    public function getFileUrl(File $file): string;

}
