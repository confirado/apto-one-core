<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Core\Query\PricePerUnitItem;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPricePerUnitPrices implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindPricePerUnitPrices constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}