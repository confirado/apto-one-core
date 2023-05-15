<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query\ImageMetaData;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;

class ImageMetaDataQueryHandler implements QueryHandlerInterface
{
    /**
     * @var MediaFileSystemConnector
     */
    private $mediaConnector;

    /**
     * @param MediaFileSystemConnector $mediaConnector
     */
    public function __construct(MediaFileSystemConnector $mediaConnector)
    {
        $this->mediaConnector = $mediaConnector;
    }

    /**
     * @param FindImageMetaData $query
     * @return array
     */
    public function handleFindImageMetaData(FindImageMetaData $query): array
    {
        $file = File::createFromPath($query->getPath());
        $imageSize = getimagesize($this->mediaConnector->getAbsolutePath($file->getPath()));

        return [
            'size' => $this->mediaConnector->getFileSize($file),
            'width' => $imageSize[0],
            'height' => $imageSize[1],
            'mime' => $imageSize['mime']
        ];
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindImageMetaData::class => [
            'method' => 'handleFindImageMetaData',
            'aptoMessageName' => 'ImageUploadFindImageMetaData',
            'bus' => 'query_bus'
        ];
    }
}
