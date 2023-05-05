<?php

namespace Apto\Catalog\Application\Core\Query\Product\Section;

use Apto\Base\Application\Core\Query\AptoFinder;

interface ProductSectionFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findPrices(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findDiscounts(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findElements(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findCustomProperties(string $id);
}