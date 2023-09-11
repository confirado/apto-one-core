<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query\RenderImage;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\FileSystem\CacheFileSystemConnector;
use Apto\Catalog\Application\Core\Query\Product\Element\ProductElementFinder;
use Apto\Catalog\Application\Core\Query\Product\Element\RenderImageFactory;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageRegistry;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class RenderImageQueryHandler implements QueryHandlerInterface
{

    /**
     * @var RenderImageFactory
     */
    private RenderImageFactory $renderImageFactory;

    /**
     * @var RenderImageRegistry
     */
    private RenderImageRegistry $renderImageRegistry;

    /**
     * @param RenderImageFactory $renderImageFactory
     * @param RenderImageRegistry $renderImageRegistry
     */
    public function __construct(
        RenderImageFactory $renderImageFactory,
        RenderImageRegistry $renderImageRegistry
    ) {
        $this->renderImageFactory = $renderImageFactory;
        $this->renderImageRegistry = $renderImageRegistry;
    }

    /**
     * @param FindEditableRenderImage $query
     * @return array
     */
    public function handleFindEditableRenderImage(FindEditableRenderImage $query): array
    {
        $state = new State($query->getState());

        foreach ($this->renderImageRegistry->getRenderImageReducers() as $renderImageReducer) {
            if ($renderImageReducer instanceof EditableRenderImageReducer) {
                $editableRenderImageReducer = $renderImageReducer;
                $editableRenderImageReducer->setRenderImageIds($query->getRenderImageIds());
                break;
            }
        }

        return $this->renderImageFactory->getRenderImagesByImageList($state, $query->getProductId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindEditableRenderImage::class => [
            'method' => 'handleFindEditableRenderImage',
            'aptoMessageName' => 'ImageUploadFindEditableRenderImage',
            'bus' => 'query_bus'
        ];
    }
}
