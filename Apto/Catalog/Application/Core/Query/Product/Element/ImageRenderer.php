<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

interface ImageRenderer
{
    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param bool $createThumb
     * @param bool $returnUrl
     * @param string|null $productId
     * @return string
     */
    public function getImageByImageList(array $imageList, string $perspective, State $state, bool $createThumb = false, bool $returnUrl = true, string $productId = null): string;

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param bool $createThumb
     * @param string|null $productId
     * @return File|null
     */
    public function getImageFileByImageList(array $imageList, string $perspective, State $state, bool $createThumb = false, string $productId = null): ?File;

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param $productId
     * @return bool
     */
    public function hasStatePerspectiveImage(array $imageList, string $perspective, State $state, $productId): bool;

    /**
     * @param State $state
     * @param array $imageList
     * @param string $perspective
     * @param bool $createThumb
     * @param bool $returnUrl
     * @return string
     */
    public function getRenderImageByImageList(State $state, array $imageList, string $perspective, bool $createThumb = false, bool $returnUrl = true): string;

    /**
     * @param array $imageList
     * @return array
     */
    public function sortImageListByLayer(array $imageList): array;

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    public function getProviderAndReducerImages(array $imageList, string $perspective, State $state, string $productId = null): array;
}
