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
                !array_key_exists('payload', $element) ||
                $element['aptoElementDefinitionId'] !== 'apto-element-image-upload' ||

                // has no render image in state
                !array_key_exists('renderImages', $element['payload']) ||
                !is_array($element['payload']['renderImages'])
            ) {
                continue;
            }

            $renderImages = $element['payload']['renderImages'];
            foreach ($renderImages as $renderImage) {
                if (
                    // render image not matches requested perspective
                    !array_key_exists('perspective', $renderImage) ||
                    $perspective !== $renderImage['perspective']
                ) {
                    continue;
                }

                // add render image to list
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
        }

        return $imageList;
    }
}
