<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindBasketConfigurationsByIdList implements PublicQueryInterface
{
    /**
     * @var array 
     */
    private $idList;

    /**
     * FindBasketConfigurationsByIdList constructor.
     * @param array $idList
     */
    public function __construct(array $idList = [])
    {
        $this->idList = $idList;
    }

    /**
     * @return array
     */
    public function getIdList(): array
    {
        return $this->idList;
    }
}