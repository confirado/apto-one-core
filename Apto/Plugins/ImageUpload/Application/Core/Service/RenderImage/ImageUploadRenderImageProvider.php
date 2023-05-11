<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Service\RenderImage;

use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageProvider;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class ImageUploadRenderImageProvider implements RenderImageProvider
{
    public function getRenderImageList(string $perspective, State $state, ?string $productId = NULL): array
    {
        $imageList = [];

        foreach ($state->getElementList() as $element) {
            if (
                // is not a special element
                !is_array($element) ||

                // is not the image-upload special element
                !array_key_exists('aptoElementDefinitionId', $element) ||
                $element['aptoElementDefinitionId'] !== 'apto-element-image-upload' ||

                // has no render image in state
                !array_key_exists('renderImage', $element) ||

                // has no render image in state
                !is_array($element['renderImage']) ||

                // has no render image in state
                !array_key_exists('perspective', $element['renderImage']) ||

                // render image not matches requested perspective
                $perspective !== $element['renderImage']['perspective']
            ) {
                continue;
            }

            // add render image to list
            $renderImage = $element['renderImage'];
            $imageList[] = [
                'productId' => $renderImage['productId'],
                'renderImageId' => $renderImage['renderImageId'],
                'perspective' => $renderImage['perspective'],
                'layer' => $renderImage['layer'],
                'offsetX' => $renderImage['offsetX'],
                'offsetY' => $renderImage['offsetY'],
                'path' => $renderImage['directory'],
                'filename' => $renderImage['fileName'],
                'extension' => $renderImage['extension'],
                'renderImageOptions' => null
            ];
        }

        return $imageList;
    }
}
