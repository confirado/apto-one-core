<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query\Canvas;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;

class CanvasQueryHandler implements QueryHandlerInterface
{
    /**
     * @var CanvasFinder
     */
    private $canvasFinder;

    /**
     * @param CanvasFinder $canvasFinder
     */
    public function __construct(CanvasFinder $canvasFinder)
    {
        $this->canvasFinder = $canvasFinder;
    }

    /**
     * @param FindCanvas $query
     * @return array|null
     * @throws InvalidUuidException
     */
    public function handleFindCanvas(FindCanvas $query): ?array
    {
        return $this->canvasFinder->findById(new AptoUuid($query->getId()));
    }

    /**
     * @param FindCanvasList $query
     * @return array
     */
    public function handleFindCanvasList(FindCanvasList $query): array
    {
        return $this->canvasFinder->findList(
            $query->getPageNumber(),
            $query->getRecordsPerPage(),
            $query->getSearchString()
        );
    }

    /**
     * @param FindCanvasIds $query
     * @return array
     */
    public function handleFindCanvasIds(FindCanvasIds $query): array
    {
        return $this->canvasFinder->findIds();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindCanvas::class => [
            'method' => 'handleFindCanvas',
            'aptoMessageName' => 'ImageUploadFindCanvas',
            'bus' => 'query_bus'
        ];

        yield FindCanvasList::class => [
            'method' => 'handleFindCanvasList',
            'aptoMessageName' => 'ImageUploadFindCanvasList',
            'bus' => 'query_bus'
        ];

        yield FindCanvasIds::class => [
            'method' => 'handleFindCanvasIds',
            'aptoMessageName' => 'ImageUploadFindCanvasIds',
            'bus' => 'query_bus'
        ];
    }
}
