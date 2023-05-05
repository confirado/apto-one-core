<?php

namespace Apto\Base\Application\Backend\Commands\Cache;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Domain\Core\Model\FileSystem\CacheFileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\ThumbFileSystemConnector;

class ClearAptoCacheHandler implements CommandHandlerInterface
{

    /**
     * @var ThumbFileSystemConnector
     */
    protected $thumbFilesystem;

    /**
     * @var CacheFileSystemConnector
     */
    protected $cacheFilesystem;

    /**
     * @param ThumbFileSystemConnector $thumbFilesystem
     * @param CacheFileSystemConnector $cacheFilesystem
     */
    public function __construct(ThumbFileSystemConnector $thumbFilesystem, CacheFileSystemConnector $cacheFilesystem)
    {
        $this->thumbFilesystem = $thumbFilesystem;
        $this->cacheFilesystem = $cacheFilesystem;
    }

    /**
     * @param ClearAptoCache $command
     */
    public function handleClearAptoCache(ClearAptoCache $command)
    {
        $types = $command->getTypes();

        if (empty($types)) {
            $types = ['image-rendered', 'image-thumb', 'apcu'];
        }

        foreach ($types as $type) {
            $this->clearCacheType($type);
        }
    }

    /**
     * @param string $type
     */
    private function clearCacheType(string $type)
    {
        switch ($type) {
            case 'image-rendered': {
                $this->clearDirectory($this->cacheFilesystem->getAbsolutePath('/'));
                break;
            }
            case 'image-thumb': {
                $this->clearDirectory($this->thumbFilesystem->getAbsolutePath('/'));
                break;
            }
            case 'apcu': {
                AptoCacheService::clearCache();
            }
        }
    }

    /**
     * @param string $directory
     */
    private function clearDirectory(string $directory)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($directory . '/.gitkeep' === $file->getRealPath()) {
                continue;
            }

            $todo = ($file->isDir() ? 'rmdir' : 'unlink');
            $todo($file->getRealPath());
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield ClearAptoCache::class => [
            'method' => 'handleClearAptoCache',
            'bus' => 'command_bus'
        ];
    }
}