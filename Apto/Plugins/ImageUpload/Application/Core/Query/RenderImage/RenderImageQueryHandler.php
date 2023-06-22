<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query\RenderImage;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\FileSystem\CacheFileSystemConnector;
use Apto\Catalog\Application\Core\Query\Product\Element\ImageRenderer;
use Apto\Catalog\Application\Core\Query\Product\Element\ProductElementFinder;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageRegistry;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class RenderImageQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ProductElementFinder
     */
    private ProductElementFinder $productElementFinder;

    /**
     * @var ImageRenderer
     */
    private ImageRenderer $imageRenderer;

    /**
     * @var RequestStore
     */
    private RequestStore $requestStore;

    /**
     * @var CacheFileSystemConnector
     */
    private CacheFileSystemConnector $cacheFileSystemConnector;

    /**
     * @var RenderImageRegistry
     */
    private RenderImageRegistry $renderImageRegistry;

    public function __construct(
        ProductElementFinder $productElementFinder,
        ImageRenderer $imageRenderer,
        RequestStore $requestStore,
        CacheFileSystemConnector $cacheFileSystemConnector,
        RenderImageRegistry $renderImageRegistry
    ) {
        $this->productElementFinder = $productElementFinder;
        $this->imageRenderer = $imageRenderer;
        $this->requestStore = $requestStore;
        $this->cacheFileSystemConnector = $cacheFileSystemConnector;
        $this->renderImageRegistry = $renderImageRegistry;
    }

    /**
     * @param FindEditableRenderImage $query
     * @return array|null
     */
    public function handleFindEditableRenderImage(FindEditableRenderImage $query): ?array
    {
        $state = new State($query->getState());

        foreach ($this->renderImageRegistry->getRenderImageReducers() as $renderImageReducer) {
            if ($renderImageReducer instanceof EditableRenderImageReducer) {
                $editableRenderImageReducer = $renderImageReducer;
                $editableRenderImageReducer->setRenderImageIds($query->getRenderImageIds());
                break;
            }
        }

        $imageList = $this->productElementFinder->findRenderImagesByState($state, $query->getPerspective());
        $renderImageFile = $this->imageRenderer->getImageFileByImageList($imageList, $query->getPerspective(), $state, false, $query->getProductId());

        if ($renderImageFile) {
            $imageSize = getimagesize($this->cacheFileSystemConnector->getAbsolutePath($renderImageFile->getPath()));
            return [
                'perspective' => $query->getPerspective(),
                'url' => $this->requestStore->getSchemeAndHttpHost() . $this->cacheFileSystemConnector->getFileUrl($renderImageFile),
                'width' => $imageSize[0],
                'height' => $imageSize[1],
            ];
        }

        return null;
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
