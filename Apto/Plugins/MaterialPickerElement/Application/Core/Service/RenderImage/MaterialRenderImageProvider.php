<?php
namespace Apto\Plugins\MaterialPickerElement\Application\Core\Service\RenderImage;

use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageProvider;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool\PoolFinder;

class MaterialRenderImageProvider implements RenderImageProvider
{
    /**
     * @var PoolFinder
     */
    private $poolFinder;

    /**
     * MaterialRenderImageProvider constructor.
     * @param PoolFinder $poolFinder
     */
    public function __construct(PoolFinder $poolFinder)
    {
        $this->poolFinder = $poolFinder;
    }

    /**
     * @param string $perspective
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    public function getRenderImageList(string $perspective, State $state, string $productId = null): array
    {
        $imageList = [];

        foreach ($state->getElementList() as $element) {
            if (
                // is not a special element
                !is_array($element) ||

                // is not the material-picker special element
                !array_key_exists('productId', $element) ||
                !array_key_exists('poolId', $element) ||
                !array_key_exists('aptoElementDefinitionId', $element) ||
                $element['aptoElementDefinitionId'] !== 'apto-element-material-picker'
            ) {
                continue;
            }

            $poolId = $element['poolId'];
            $materials = $this->getMaterialsByElement($element);
            $renderImages = $this->poolFinder->findRenderImagesByMaterials($poolId, $materials, $perspective);

            foreach ($renderImages as &$renderImage) {
                $renderImage['productId'] = $element['productId'];
            }

            $imageList = array_merge($imageList, $renderImages);
        }

        return $imageList;
    }

    /**
     * @param array $element
     * @return array
     */
    private function getMaterialsByElement(array $element): array
    {
        $materials = [];

        if ($element['materialId']) {
            $materials[] = $element['materialId'];
        }

        if ($element['materialIdSecondary']) {
            $materials[] = $element['materialIdSecondary'];
        }

        if (array_key_exists('materials', $element) && count($element['materials']) > 0) {
            foreach ($element['materials'] as $material) {
                $materials[] = $material['id'];
            }
        }

        return $materials;
    }
}
