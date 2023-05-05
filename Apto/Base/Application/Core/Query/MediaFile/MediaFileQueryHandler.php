<?php

namespace Apto\Base\Application\Core\Query\MediaFile;

use Apto\Base\Application\Core\QueryHandlerInterface;

class MediaFileQueryHandler implements QueryHandlerInterface
{
    /**
     * @var MediaFileFinder
     */
    protected $mediaFileFinder;

    /**
     * MediaFileQueryHandler constructor.
     * @param MediaFileFinder $mediaFileFinder
     */
    public function __construct(MediaFileFinder $mediaFileFinder)
    {
        $this->mediaFileFinder = $mediaFileFinder;
    }

    /**
     * @param FindMediaFile $query
     * @return mixed
     */
    public function handleFindMediaFile(FindMediaFile $query)
    {
        return $this->mediaFileFinder->findById($query->getId());
    }

    /**
     * @param FindMediaFileByName $query
     * @return mixed
     */
    public function handleFindMediaFileByName(FindMediaFileByName $query)
    {
        return $this->mediaFileFinder->findByFile($query->getFile());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindMediaFileByName::class => [
            'method' => 'handleFindMediaFileByName',
            'bus' => 'query_bus'
        ];

        yield FindMediaFile::class => [
            'method' => 'handleFindMediaFile',
            'bus' => 'query_bus'
        ];
    }
}