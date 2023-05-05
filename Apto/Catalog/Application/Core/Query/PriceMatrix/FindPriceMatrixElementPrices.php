<?php

namespace Apto\Catalog\Application\Core\Query\PriceMatrix;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPriceMatrixElementPrices implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $elementId;

    /**
     * FindPriceMatrixElementPrices constructor.
     * @param string $id
     * @param string $elementId
     */
    public function __construct(string $id, string $elementId)
    {
        $this->id = $id;
        $this->elementId = $elementId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }
}