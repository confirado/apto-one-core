<?php

namespace Apto\Base\Application\Backend\Commands\MediaFile;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\RootReadOnlyFileSystemConnector;
use Apto\Base\Domain\Core\Service\StringSanitizer;

class UploadMediaFileHandler implements CommandHandlerInterface
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
    protected $sanitizer;

    /**
     * @var MediaFileSystemConnector
     */
    protected $mediaConnector;

    /**
     * @var RootReadOnlyFileSystemConnector
     */
    protected $rootConnector;

    /**
     * UploadMediaFileHandler constructor.
     * @param StringSanitizer $sanitizer
     * @param MediaFileSystemConnector $mediaConnector
     * @param RootReadOnlyFileSystemConnector $rootConnector
     */
    public function __construct(StringSanitizer $sanitizer, MediaFileSystemConnector $mediaConnector, RootReadOnlyFileSystemConnector $rootConnector)
    {
        $this->sanitizer = $sanitizer;
        $this->mediaConnector = $mediaConnector;
        $this->rootConnector = $rootConnector;
    }

    /**
     * @param UploadMediaFile $command
     */
    public function handle(UploadMediaFile $command)
    {
        // get command data
        $dstDirectory = new Directory($command->getDirectory());
        $overwriteExisting = $command->getOverwriteExisting();

        foreach ($command->getFiles() as $srcPath => $dstFilename) {

            // create Files for src and dst
            $srcFile = File::createFromPath($srcPath);
            $dstFile = new File($dstDirectory, $this->sanitizer->sanitizeFilename($dstFilename));

            // exclude forbidden extensions
            $dstFile->assertHasNotExtension(self::FORBIDDEN_EXTENSIONS);

            // create directory if not already exists
            if (!$this->mediaConnector->existsDirectory($dstDirectory)) {
                $this->mediaConnector->createDirectory($dstDirectory, true);
            }

            // create new file
            $this->mediaConnector->createFile($dstFile, $srcFile, $this->rootConnector, $overwriteExisting);
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield UploadMediaFile::class => [
            'method' => 'handle',
            'bus' => 'command_bus'
        ];
    }
}