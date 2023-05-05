<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Base\Application\Core\Query\AptoFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

interface ProductElementFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param array $ids
     * @return array
     */
    public function findElementsByIds(array $ids);

    /**
     * @param string $id
     * @return array|null
     */
    public function findPrices(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findPriceFormulas(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findDiscounts(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findRenderImages(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findCustomProperties(string $id);

    /**
     * @param State $state
     * @param string $perspective
     * @return array
     */
    public function findRenderImagesByState(State $state, string $perspective);

    /**
     * @param State $state
     * @return array
     */
    public function findElementDefinitionsByState(State $state);

    /**
     * @param string $id
     * @return array|null
     */
    public function findAttachments(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findGallery(string $id);
}
