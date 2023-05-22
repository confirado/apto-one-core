<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query\RenderImage;

use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageReducer;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class EditableRenderImageReducer implements RenderImageReducer
{
    /**
     * @var string|null
     */
    private ?string $renderImageHash;

    /**
     * @param string|null $renderImageHash
     */
    public function __construct(string $renderImageHash = null)
    {
        $this->renderImageHash = $renderImageHash;
    }

    /**
     * @param string|null $renderImageHash
     */
    public function setRenderImageHash(?string $renderImageHash): void
    {
        $this->renderImageHash = $renderImageHash;
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
        if (null === $this->renderImageHash) {
            return $imageList;
        }

        foreach ($imageList as $key => $image) {
            if ($image['renderImageId'] === $this->renderImageHash) {
                unset($imageList[$key]);
            }
        }
        return $imageList;
    }
}
