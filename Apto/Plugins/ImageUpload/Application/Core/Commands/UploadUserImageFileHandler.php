<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Commands;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemMountedReadOnlyException;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\File\FileForbiddenExtension;
use Apto\Base\Domain\Core\Model\FileSystem\FileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\RootFileSystemConnector;
use Apto\Base\Domain\Core\Service\StringSanitizer;
use Imagick;
use ImagickException;
use RuntimeException;

class UploadUserImageFileHandler implements CommandHandlerInterface
{
    const FORBIDDEN_EXTENSIONS = [
        // empty extension
        '',
        // executables, batch files, links, shell scripts
        'exe', 'com', 'bat', 'lnk', 'sh',
        // server side scripts
        'php', 'pht', 'phtml', 'shtml', 'php4', 'php5', 'php7', 'cgi', 'asp', 'aspx',
        // archives
        'zip', 'rar', 'tar', 'gz', '7z',
        // certificates
        'cer',
        // client side scripts
        'js', 'vba', 'vbs',
        // webserver config
        'htaccess', 'htpasswd'
    ];

    /**
     * @var StringSanitizer
     */
    protected StringSanitizer $sanitizer;

    /**
     * @var FileSystemConnector
     */
    protected FileSystemConnector $mediaConnector;

    /**
     * @var FileSystemConnector
     */
    protected FileSystemConnector $rootConnector;

    /**
     * @param StringSanitizer $sanitizer
     * @param MediaFileSystemConnector $mediaConnector
     * @param RootFileSystemConnector $rootConnector
     */
    public function __construct(StringSanitizer $sanitizer, MediaFileSystemConnector $mediaConnector, RootFileSystemConnector $rootConnector)
    {
        $this->sanitizer = $sanitizer;
        $this->mediaConnector = $mediaConnector;
        $this->rootConnector = $rootConnector;
    }

    /**
     * @param UploadUserImageFile $command
     * @return void
     * @throws DirectoryNotCreatableException
     * @throws FileForbiddenExtension
     * @throws FileNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     */
    public function handle(UploadUserImageFile $command)
    {
        // get command data
        $dstDirectory = new Directory($command->getPath());
        $overwriteExisting = true;
        $aptoUuid = $command->getHash();
        $extension = $command->getExtension();

        foreach ($command->getFiles() as $srcPath => $orgFilename) {

            // create Files for src and dst
            $srcFile = File::createFromPath($srcPath);
            $dstFile = new File($dstDirectory, $this->sanitizer->sanitizeFilename($aptoUuid . '.' . $extension));

            // exclude forbidden extensions
            $dstFile->assertHasNotExtension(self::FORBIDDEN_EXTENSIONS);

            if ($dstFile->hasExtension(["eps"])) {
                $srcFile = $this->handleSpecialFileTypes($srcPath);
                $dstFile = new File($dstDirectory, $this->sanitizer->sanitizeFilename($aptoUuid . '.png'));
            }

            // create directory if not already exists
            if (!$this->mediaConnector->existsDirectory($dstDirectory)) {
                $this->mediaConnector->createDirectory($dstDirectory, true);
            }

            // create new file
            $this->mediaConnector->createFile($dstFile, $srcFile, $this->rootConnector, $overwriteExisting);

            // return after one file because only one file per upload is allowed
            return;
        }
    }

    private function handleSpecialFileTypes(string $epsPath): File
    {
        try {
            $imagick = new Imagick();
            $imagick->readImage($epsPath);
            $imagick = $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
            $imagick->setImageFormat('png');
            $tempPngPath = sys_get_temp_dir() . '/' . uniqid('tmpImgFile_', true) . '.png';
            $imagick->writeImage($tempPngPath);
            return File::createFromPath($tempPngPath);
        } catch (ImagickException $e) {
            throw new RuntimeException('Error converting EPS to PNG: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield UploadUserImageFile::class => [
            'method' => 'handle',
            'bus' => 'command_bus'
        ];
    }
}
