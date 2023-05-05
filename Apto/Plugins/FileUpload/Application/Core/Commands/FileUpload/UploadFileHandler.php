<?php

namespace Apto\Plugins\FileUpload\Application\Core\Commands\FileUpload;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemMountedReadOnlyException;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\File\FileForbiddenExtension;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\RootFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\StringSanitizer;

class UploadFileHandler extends AbstractCommandHandler
{
    const FORBIDDEN_EXTENSIONS = [
        // empty extension
        '',
        // executables, batch files, links, shell scripts
        'exe', 'com', 'bat', 'lnk', 'sh',
        // server side scripts
        'php', 'pht', 'phtml', 'shtml', 'php4', 'php5', 'php7', 'cgi', 'asp', 'aspx',
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
    protected $sanitizer;

    /**
     * @var MediaFileSystemConnector
     */
    protected $mediaConnector;

    /**
     * @var RootFileSystemConnector
     */
    protected $rootConnector;

    /**
     * UploadFileHandler constructor.
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
     * @param UploadFile $command
     * @throws FileForbiddenExtension
     * @throws FileNotCreatableException
     * @throws DirectoryNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     * @throws InvalidUuidException
     */
    public function handleUploadFile(UploadFile $command)
    {
        // get command data
        $dstDirectory = new Directory($command->getPath());
        $overwriteExisting = true;
        $aptoUuid = new AptoUuid($command->getAptoUuid());
        $extension = $command->getExtension();

        foreach ($command->getFiles() as $srcPath => $orgFilename) {

            // create Files for src and dst
            $srcFile = File::createFromPath($srcPath);
            $dstFile = new File($dstDirectory, $this->sanitizer->sanitizeFilename($aptoUuid->getId() . '.' . $extension));

            // exclude forbidden extensions
            $dstFile->assertHasNotExtension(self::FORBIDDEN_EXTENSIONS);

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

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield UploadFile::class => [
            'method' => 'handleUploadFile',
            'aptoMessageName' => 'PluginFileUploadUploadFile',
            'bus' => 'command_bus'
        ];
    }
}