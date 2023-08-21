<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\ImageRenderer;

use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\FileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageProvider;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageReducer;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageRegistry;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\RenderImage;
use Apto\Catalog\Application\Core\Query\Product\Element\RenderImageFactory as RenderImageFactoryInterface;

class RenderImageFactory implements RenderImageFactoryInterface
{
    use FormulaParser;

    /**
     * @var MediaFileSystemConnector
     */
    private MediaFileSystemConnector $srcFilesystem;

    /**
     * @var RenderImageRegistry
     */
    private RenderImageRegistry $renderImageRegistry;

    /**
     * @param MediaFileSystemConnector $srcFilesystem
     * @param RenderImageRegistry $renderImageRegistry
     */
    public function __construct(
        MediaFileSystemConnector $srcFilesystem,
        RenderImageRegistry $renderImageRegistry,
    ) {
        $this->srcFilesystem = $srcFilesystem;
        $this->renderImageRegistry = $renderImageRegistry;
    }

    public function getRenderImagesByImageList(array $imageList, string $perspective, State $state, string $productId = null): array
    {
        $imageList = $this->getProviderAndReducerImages($imageList, $perspective, $state, $productId);

        if (empty($imageList)) {
            return [];
        }

        $imageList = $this->sortImageListByLayer($imageList);
        $srcFiles = $this->getSrcFiles($imageList);

        return $this->getRenderedImagesData($state, $this->srcFilesystem, $srcFiles, $imageList);
    }

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    private function getProviderAndReducerImages(array $imageList, string $perspective, State $state, string $productId = null): array
    {
        $providerImageList = $this->getProviderImageList($perspective, $state, $productId);
        $imageList = array_merge($imageList, $providerImageList);

        return $this->getReducerImageList($imageList, $perspective, $state, $productId);
    }

    /**
     * @param string $perspective
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    private function getProviderImageList(string $perspective, State $state, string $productId = null): array
    {
        $renderImageProviders = $this->renderImageRegistry->getRenderImageProviders();
        $providerImageList = [];

        /** @var RenderImageProvider $renderImageProvider */
        foreach ($renderImageProviders as $renderImageProvider) {
            $providerImageList = array_merge($providerImageList, $renderImageProvider->getRenderImageList($perspective, $state, $productId));
        }

        return $providerImageList;
    }

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    private function getReducerImageList(array $imageList, string $perspective, State $state, string $productId = null): array
    {
        $renderImageReducers = $this->renderImageRegistry->getRenderImageReducers();

        /** @var RenderImageReducer $renderImageReducer */
        foreach ($renderImageReducers as $renderImageReducer) {
            $imageList = $renderImageReducer->getRenderImageList($perspective, $state, $imageList, $productId);
        }

        return $imageList;
    }

    /**
     * Sort by layer asc
     *
     * @param array $imageList
     * @return array
     */
    private function sortImageListByLayer(array $imageList): array
    {
        usort($imageList, fn(array $a, array $b) => $a['layer'] <=> $b['layer']);

        return $imageList;
    }

    /**
     * Get a list of all src images with their full paths
     *
     * @param array $imageList
     *
     * @return array
     */
    private function getSrcFiles(array $imageList): array
    {
        $srcFiles = [];
        foreach ($imageList as $image) {
            $srcDirectory = new Directory($image['path']);
            $srcFiles[] = new File($srcDirectory, $image['filename'] . '.' . $image['extension']);;
        }

        return $srcFiles;
    }

    /**
     * Collects the data for all render images for the given product
     *
     * This array will be used in frontend to draw the canvas with these images
     *
     * @param State               $state
     * @param FileSystemConnector $srcFileSystem
     * @param array               $srcFiles
     * @param array               $imageList
     *
     * @return array
     */
    private function getRenderedImagesData(State $state, FileSystemConnector $srcFileSystem, array $srcFiles, array $imageList = []): array
    {
        $images = [];
        foreach ($srcFiles as $index => $srcFile) {
            $currentImage = $imageList[$index];

            $renderImageOptions = null;
            if (array_key_exists('renderImageOptions', $currentImage)) {
                $renderImageOptions = $currentImage['renderImageOptions'];
            }

            $dimensions = getimagesize($srcFileSystem->getAbsolutePath($srcFile->getPath()));
            $currentImage['realWidth'] = (int)$dimensions[0];
            $currentImage['realHeight'] = (int)$dimensions[1];

            // replace src file with repeated version, if requested
            if ($renderImageOptions && $renderImageOptions['renderImageOptions']['type'] === 'Wiederholbar') {
                $renderImageDimensions = $this->getRepeatableRenderImageDimensions($state, $renderImageOptions);
                $currentImage['realWidth'] = (int)$renderImageDimensions['width'];
                $currentImage['realHeight'] = (int)$renderImageDimensions['height'];
            }

            // calculate image at offset
            if ($renderImageOptions && $renderImageOptions['offsetOptions']['type'] === 'Berechnend') {
                $offset = $this->getCalculatedOffset($state, $renderImageOptions);
            } else if ($currentImage['offsetX'] != 0 || $currentImage['offsetY'] != 0 ) {
                // when you save static offset in render image then switch to calculated the old static value stays
                $offset = $this->getStaticOffset($currentImage, $dimensions);
            } else {
                $offset = ['x' => 0, 'y' => 0];
            }

            $currentImage['realOffsetX'] = (int)$offset['x'];
            $currentImage['realOffsetY'] = (int)$offset['y'];

            $images[] = $currentImage;
        }

        return $images;
    }

    /**
     * @param State $state
     * @param array|null $renderImageOptions
     * @return array
     */
    private function getRepeatableRenderImageDimensions(State $state, ?array $renderImageOptions): array
    {
        $result = [
            'width' => '',
            'height' => ''
        ];

        if (!$renderImageOptions) {
            return $result;
        }

        $renderImageOptions = $renderImageOptions['renderImageOptions'];
        $formulaAliases = $this->getFormulaAliases($state, $renderImageOptions);

        // calculate width and height (if image is static then we have null)
        if (null !== ($formulaResult = self::calculateFormula($renderImageOptions['formulaHorizontal'], $formulaAliases))) {
            $result['width'] = $formulaResult;
        }

        if (null !== ($formulaResult = self::calculateFormula($renderImageOptions['formulaVertical'], $formulaAliases))) {
            $result['height'] = $formulaResult;
        }

        return $result;
    }

    /**
     * Get calculated offset
     *
     * @param State      $state
     * @param array|null $renderImageOptions
     *
     * @return int[]
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     * @throws \Apto\Base\Domain\Core\Service\AptoJsonSerializerException
     */
    private function getCalculatedOffset(State $state, ?array $renderImageOptions): array
    {
        $offset = [
            'x' => 0,
            'y' => 0
        ];

        if (!$renderImageOptions) {
            return $offset;
        }

        $offsetOptions = $renderImageOptions['offsetOptions'];
        $formulaAliases = $this->getFormulaAliases($state, $offsetOptions);

        // calculate x and y offset
        if (null !== ($formulaResult = self::calculateFormula($offsetOptions['formulaOffsetX'], $formulaAliases))) {
            $offset['x'] = $formulaResult;
        }

        if (null !== ($formulaResult = self::calculateFormula($offsetOptions['formulaOffsetY'], $formulaAliases))) {
            $offset['y'] = $formulaResult;
        }

        return $offset;
    }

    /**
     * Get static offset
     * @param array $currentImage
     * @param array $dimensions
     * @return array
     */
    private function getStaticOffset(array $currentImage, array $dimensions): array
    {
        return [
            'x' => self::calculateOffset(
                $currentImage['offsetX'],
                $currentImage['renderImageOptions']['offsetOptions']['offsetUnitX'] ?? RenderImage::OFFSET_UNIT_PERCENT,
                $dimensions[0]
            ),
            'y' => self::calculateOffset(
                $currentImage['offsetY'],
                $currentImage['renderImageOptions']['offsetOptions']['offsetUnitY'] ?? RenderImage::OFFSET_UNIT_PERCENT,
                $dimensions[1]
            )
        ];
    }

    /**
     * Calculate offset depending on selected unit and given size
     *
     * @param float $offset
     * @param int   $unit
     * @param int   $size
     *
     * @return float
     */
    private static function calculateOffset(float $offset, int $unit, int $size): float
    {
        switch ($unit)
        {
            // use relative offset in percent relative to size
            case RenderImage::OFFSET_UNIT_PERCENT:
                return $size * $offset / 100;

            // use absolute offset in pixel
            case RenderImage::OFFSET_UNIT_PIXEL:
                return floor($offset);

            // invalid offset unit detected
            default:
                throw new \InvalidArgumentException(sprintf(
                    'The given value "%s" is not within the valid offset unit types (%s).',
                    $unit,
                    implode(', ', RenderImage::VALID_OFFSET_UNITS)
                ));
        }
    }
}
