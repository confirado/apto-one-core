<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query\RenderImage;

use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageReducer;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class EditableRenderImageReducer implements RenderImageReducer
{
    /**
     * @var array
     */
    private array $renderImageIds;

    /**
     * @param array $renderImageIds
     */
    public function __construct(array $renderImageIds = [])
    {
        $this->renderImageIds = $renderImageIds;
    }

    /**
     * @param array $renderImageIds
     * @return void
     */
    public function setRenderImageIds(array $renderImageIds): void
    {
        $this->renderImageIds = $renderImageIds;
    }

    /**
     * @param string $perspective
     * @param State $state
     * @param array $imageList
     * @param string|null $productId
     * @return array
     */
    public function getRenderImageList(string $perspective, State $state, array $imageList, string $productId = null): array
    {
        foreach ($imageList as $key => $image) {
            foreach ($this->renderImageIds as $renderImageId) {
                if ($image['renderImageId'] === $renderImageId) {
                    unset($imageList[$key]);
                }
            }
        }
        return $imageList;
    }
}
