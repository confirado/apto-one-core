<?php

namespace Apto\Base\Application\Backend\Query\MediaFile;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;

class ListMediaFilesHandler implements QueryHandlerInterface
{
    /**
     * @var MediaFileSystemConnector
     */
    protected $connector;

    /**
     * ListMediaFilesHandler constructor.
     * @param MediaFileSystemConnector $connector
     */
    public function __construct(MediaFileSystemConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param ListMediaFiles $query
     * @return mixed
     */
    public function handle(ListMediaFiles $query)
    {
        $directory = Directory::createFromPath($query->getDirectory());
        return $this->connector->getDirectoryContent($directory, $query->getAllowedExtensions());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield ListMediaFiles::class => [
            'method' => 'handle',
            'bus' => 'query_bus'
        ];
    }

}