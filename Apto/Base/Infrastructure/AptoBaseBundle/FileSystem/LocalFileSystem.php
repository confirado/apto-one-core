<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\FileSystem;

use Apto\Base\Domain\Core\Model\FileSystem\FileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemMountedReadOnlyException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotRemovableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryPermissionSetFailedException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotRemovableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FilePermissionSetFailedException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemInvalidRootDirectoryException;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;

abstract class LocalFileSystem implements FileSystemConnector
{
    /**
     * @var Directory
     */
    protected $rootDirectory;

    /**
     * @var string
     */
    protected $urlPrefix;

    /**
     * @var int
     */
    protected $defaultPermission;

    /**
     * @var bool
     */
    protected $readOnly;

    /**
     * @param AptoParameterInterface $aptoParameter
     * @param string $rootDirectory
     * @param string $urlPrefix
     * @param bool $readOnly
     * @throws FileSystemInvalidRootDirectoryException
     */
    public function __construct(AptoParameterInterface $aptoParameter, string $rootDirectory, string $urlPrefix, bool $readOnly = false)
    {
        $this->rootDirectory = Directory::createFromPath(realpath($rootDirectory));
        $this->urlPrefix = $urlPrefix;
        $this->readOnly = $readOnly;

        $this->defaultPermission = $aptoParameter->get('default_permission');

        if (!is_dir($this->rootDirectory->getPath())) {
            throw new FileSystemInvalidRootDirectoryException($this->rootDirectory->getPath());
        }
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getAbsolutePath(string $path): string
    {
        // ---begin---
        // @todo: hotfix for environments where root_file_system cant be '/'
        $rootDirectoryPath = $this->rootDirectory->getPath();
        $phpTmpDir = sys_get_temp_dir();
        if (substr($path, 0, strlen($phpTmpDir)) === $phpTmpDir) {
            $rootDirectoryPath = '/';
        }
        // ---end---

        // @todo: hotfix for windows environments, please improve and do security evaluation
        // maybe the upload commands should be adjusted accordingly?!
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if (substr($path, 1, 1) === ':') {
                $path = substr($path, 2);
            }
            $absolutePath = rtrim($rootDirectoryPath . ltrim($path, '/'), '/');
            $absolutePath = str_replace('/', '\\', $absolutePath);
        } else {
            if ($rootDirectoryPath !== '/') {
                // remove rootDirectoryPath that is already included in path
                // this fix is necessary for paths that are already absolut paths
                // @todo find a better solution for the problem when we dont have access to "/" of the filesystem, something like entry path or so
                $pos = strpos($path, $rootDirectoryPath);
                if ($pos !== false) {
                    $path = substr_replace($path, '', $pos, strlen($rootDirectoryPath));
                }
            }
            $absolutePath = rtrim($rootDirectoryPath . ltrim($path, '/'), '/');
        }

        return $absolutePath;
    }

    /**
     * @param File $file
     * @return bool
     */
    public function existsFile(File $file): bool
    {
        $path = $this->getAbsolutePath($file->getPath());
        return is_file($path);
    }

    /**
     * @param Directory $directory
     * @return bool
     */
    public function existsDirectory(Directory $directory): bool
    {
        $path = $this->getAbsolutePath($directory->getPath());
        return is_dir($path);
    }

    /**
     * @param File $file
     * @return bool
     */
    public function isFileEmpty(File $file): bool
    {
        $path = $this->getAbsolutePath($file->getPath());
        return 0 == filesize($path);
    }

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryEmpty(Directory $directory): bool
    {
        $path = $this->getAbsolutePath($directory->getPath());
        $iterator = new \FilesystemIterator($path);
        return !$iterator->valid();
    }

    /**
     * @param File $file
     * @return bool
     */
    public function isFileReadable(File $file): bool
    {
        $path = $this->getAbsolutePath($file->getPath());
        return is_readable($path);
    }

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryReadable(Directory $directory): bool
    {
        $path = $this->getAbsolutePath($directory->getPath());
        return is_readable($path);
    }

    /**
     * @param File $file
     * @return bool
     */
    public function isFileWritable(File $file): bool
    {
        if ($this->readOnly) {
            return false;
        }

        $path = $this->getAbsolutePath($file->getPath());
        return is_writable($path);
    }

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryWritable(Directory $directory): bool
    {
        if ($this->readOnly) {
            return false;
        }

        $path = $this->getAbsolutePath($directory->getPath());
        return is_writable($path);
    }

    /**
     * @param File $file
     * @param File|null $source
     * @param FileSystemConnector $sourceConnector
     * @param bool $overwriteExisting
     * @return bool
     */
    public function isFileCreatable(File $file, File $source = null, FileSystemConnector $sourceConnector, bool $overwriteExisting = false): bool
    {
        if ($this->readOnly) {
            return false;
        }

        if (!$overwriteExisting && $this->existsFile($file)) {
            return false;
        }

        if ($source) {
            $srcPath = $sourceConnector->getAbsolutePath($source->getPath());
            if (!is_readable($srcPath)) {
                return false;
            }
        }

        $path = $this->getAbsolutePath($file->getPath());
        return is_writable($path);
    }

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryCreatable(Directory $directory): bool
    {
        if ($this->readOnly) {
            return false;
        }

        $path = $this->getAbsolutePath($directory->getPath());
        return is_writable($path);
    }

    /**
     * @param File $file destination file
     * @param File|null $source source file
     * @param FileSystemConnector|null $sourceConnector
     * @param bool $overwriteExisting
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     * @throws FileNotCreatableException
     */
    public function createFile(File $file, File $source = null, FileSystemConnector $sourceConnector = null, bool $overwriteExisting = false): FileSystemConnector
    {
        if ($this->readOnly) {
            throw new FileSystemMountedReadOnlyException();
        }

        $path = $this->getAbsolutePath($file->getPath());

        // prevent overwriting of existing file
        if (!$overwriteExisting && $this->existsFile($file)) {
            throw new FileNotCreatableException($path, 'The destination file already exists and overwriteExisting set to false.');
        }

        if (!$source || !$sourceConnector) {

            // create an empty file
            if (!@touch($path)) {
                throw new FileNotCreatableException($path, 'An empty file could not be created.');
            }

        } else if ($sourceConnector instanceof LocalFileSystem) {

            // try to move file from src to dst
            $srcPath = $sourceConnector->getAbsolutePath($source->getPath());

            if (is_uploaded_file($srcPath)) {
                // move uploaded file
                if (!@move_uploaded_file($srcPath, $path)) {
                    throw new FileNotCreatableException($path, 'The uploaded file \'' . $srcPath . '\' could not be moved.');
                }
            } else {
                // copy file
                if (!@copy($srcPath, $path)) {
                    throw new FileNotCreatableException($path, 'The file \'' . $srcPath . '\' could not be copied.');
                }
            }

        } else {

            // copy contents
            $content = $sourceConnector->getFileContent($source);
            file_put_contents($file->getPath(), $content);

        }

        // set default permissions
        if (!chmod($path, $this->defaultPermission)) {
            unlink($path);
            throw new FileNotCreatableException($this->getAbsolutePath($file->getPath()), 'The destination\'s file permission could not be set.');
        }

        return $this;
    }

    /**
     * @param Directory $directory
     * @param string $newPath
     * @return FileSystemConnector
     * @throws DirectoryNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     */
    public function renameDirectory(Directory $directory, string $newPath): FileSystemConnector
    {
        if ($this->readOnly) {
            throw new FileSystemMountedReadOnlyException();
        }

        $path = $this->getAbsolutePath($directory->getPath());

        if (!@rename($path, $newPath)) {
            throw new DirectoryNotCreatableException($newPath);
        }

        return $this;
    }

    /**
     * @param Directory $directory
     * @param bool $recursive
     * @return FileSystemConnector
     * @throws DirectoryNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     */
    public function createDirectory(Directory $directory, bool $recursive = false): FileSystemConnector
    {
        if ($this->readOnly) {
            throw new FileSystemMountedReadOnlyException();
        }

        $path = $this->getAbsolutePath($directory->getPath());
        if (!@mkdir($path, $this->defaultPermission, $recursive)) {
            throw new DirectoryNotCreatableException($this->getAbsolutePath($directory->getPath()));
        }

        return $this;
    }

    /**
     * @param File $file
     * @return bool
     */
    public function isFileRemovable(File $file): bool
    {
        if ($this->readOnly) {
            return false;
        }

        $path = $this->getAbsolutePath($file->getPath());

        return is_writable($path);
    }

    /**
     * @param Directory $directory
     * @return bool
     */
    public function isDirectoryRemovable(Directory $directory): bool
    {
        if ($this->readOnly) {
            return false;
        }

        $path = $this->getAbsolutePath($directory->getPath());

        if (!$this->isDirectoryEmpty($directory)) {
            return false;
        }

        return is_writable($path);
    }

    /**
     * @param File $file
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     * @throws FileNotRemovableException
     */
    public function removeFile(File $file): FileSystemConnector
    {
        if ($this->readOnly) {
            throw new FileSystemMountedReadOnlyException();
        }

        $path = $this->getAbsolutePath($file->getPath());
        if (!@unlink($path)) {
            throw new FileNotRemovableException($this->getAbsolutePath($file->getPath()));
        }

        return $this;
    }

    /**
     * @param Directory $directory
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     * @throws DirectoryNotRemovableException
     */
    public function removeDirectory(Directory $directory): FileSystemConnector
    {
        if ($this->readOnly) {
            throw new FileSystemMountedReadOnlyException();
        }

        $path = $this->getAbsolutePath($directory->getPath());

        if (!$this->isDirectoryEmpty($directory)) {
            throw new DirectoryNotRemovableException($path, 'The directory is not empty.');
        }

        if (!@rmdir($path)) {
            throw new DirectoryNotRemovableException($path);
        }

        return $this;
    }

    /**
     * Empties/clears the given directory, does not deletes the directory itself
     *
     * @param Directory $directory
     *
     * @return void
     */
    public function clearDirectory(Directory $directory): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->getAbsolutePath($directory->getPath()), \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $todo = ($file->isDir() ? 'rmdir' : 'unlink');
            $todo($file->getRealPath());
        }
    }

    /**
     * @param File $file
     * @return int
     */
    public function getFilePermission(File $file): int
    {
        $path = $this->getAbsolutePath($file->getPath());
        return fileperms($path);
    }

    /**
     * @param Directory $directory
     * @return int
     */
    public function getDirectoryPermission(Directory $directory): int
    {
        $path = $this->getAbsolutePath($directory->getPath());
        return fileperms($path);
    }

    /**
     * @param File $file
     * @param int|null $permission
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     * @throws FilePermissionSetFailedException
     */
    public function setFilePermission(File $file, int $permission = null): FileSystemConnector
    {
        if ($this->readOnly) {
            throw new FileSystemMountedReadOnlyException();
        }

        $path = $this->getAbsolutePath($file->getPath());
        if (!@chmod($path, $permission ?? $this->defaultPermission)) {
            throw new FilePermissionSetFailedException($path);
        }

        return $this;
    }

    /**
     * @param Directory $directory
     * @param int|null $permission
     * @return FileSystemConnector
     * @throws FileSystemMountedReadOnlyException
     * @throws DirectoryPermissionSetFailedException
     */
    public function setDirectoryPermission(Directory $directory, int $permission = null): FileSystemConnector
    {
        if ($this->readOnly) {
            throw new FileSystemMountedReadOnlyException();
        }

        $path = $this->getAbsolutePath($directory->getPath());
        if (!@chmod($path, $permission ?? $this->defaultPermission)) {
            throw new DirectoryPermissionSetFailedException($path);
        }

        return $this;
    }

    /**
     * @param File $file
     * @return string
     */
    public function getFileContent(File $file): string
    {
        $path = $this->getAbsolutePath($file->getPath());
        return file_get_contents($path);
    }

    /**
     * @param Directory $directory
     * @param array $allowedExtensions
     * @return array
     */
    public function getDirectoryContent(Directory $directory, array $allowedExtensions = []): array
    {
        $path = $this->getAbsolutePath($directory->getPath());
        $dir = new \DirectoryIterator($path);
        $length = strlen($this->rootDirectory->getPath()) - 1;
        $dataFiles = [];
        $dataFolders = [];

        foreach ($dir as $file) {

            // skip folders . and ..
            if ($file->isDot()) {
                continue;
            }

            // skip hidden files
            if ('.' == substr($file->getFilename(), 0, 1)) {
                continue;
            }

            // skip forbidden extensions...
            if ($file->isFile() && !empty($allowedExtensions) && !in_array(strtolower($file->getExtension()), $allowedExtensions)) {
                continue;
            }

            $url = rtrim($this->urlPrefix, '/') . $directory->getPath() . $file->getFilename();

            $data = [
                'name' => $file->getFilename(),
                'path' => substr($file->getPath(), $length) . '/' . $file->getFilename(),
                'directory' => substr($file->getPath(), $length),
                'isDir' => $file->isDir(),
                'extension' => $file->getExtension(),
                'size' => $file->isDir() ? -1 : $file->getSize(),
                'url' => $file->isDir() ? false : $url
            ];

            if ($file->isDir()) {
                $dataFolders[] = $data;
            } else {
                $dataFiles[] = $data;
            }
        }

        return array_merge(
            $this->natSortArrayByProperty($dataFolders, 'name'),
            $this->natSortArrayByProperty($dataFiles, 'name')
        );
    }

    /**
     * @param File $file
     * @return int
     */
    public function getFileSize(File $file): int
    {
        $path = $this->getAbsolutePath($file->getPath());
        return filesize($path);
    }

    /**
     * @param File $file
     * @return string
     */
    public function getFileMd5Hash(File $file): string
    {
        $path = $this->getAbsolutePath($file->getPath());
        return md5_file($path);
    }

    /**
     * @param File $file
     * @return string
     */
    public function getFileUrl(File $file): string
    {
        return rtrim($this->urlPrefix, '/') . '/' . ltrim($file->getPath(), '/');
    }

    /**
     * @internal dont use that function with associative arrays, you will lost key names
     * @param $array
     * @param $property
     * @return array
     */
    private function natSortArrayByProperty(array $array, string $property): array
    {
        $sortArray = [];
        $sortedArray = [];

        // add every value to sort array for access by key
        foreach ($array as $key => $value) {
            $sortValue = '';
            if (array_key_exists($property, $value)) {
                $sortValue = $value[$property];
            }
            $sortArray[$sortValue . $key] = $value;
        }

        // get keys as array
        $keys = array_keys($sortArray);

        // naturally sort keys
        natsort($keys);

        // add each value according to the order of the keys
        foreach ($keys as $key) {
            $sortedArray[] = $sortArray[$key];
        }

        // return sorted array
        return $sortedArray;
    }
}
