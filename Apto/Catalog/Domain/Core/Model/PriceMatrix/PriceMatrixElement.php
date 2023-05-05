<?php
namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPrices;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Doctrine\Common\Collections\ArrayCollection;

class PriceMatrixElement extends AptoEntity
{
    use AptoPrices;
    use AptoCustomProperties;

    /**
     * @var PriceMatrix
     */
    private $matrix;

    /**
     * @var PriceMatrixPosition
     */
    private $position;

    /**
     * PriceMatrix constructor.
     * @param AptoUuid $id
     * @param PriceMatrix $matrix
     * @param PriceMatrixPosition $position
     */
    public function __construct(AptoUuid $id, PriceMatrix $matrix, PriceMatrixPosition $position)
    {
        parent::__construct($id);
        $this->matrix = $matrix;
        $this->position = $position;
        $this->aptoPrices = new ArrayCollection();
        $this->customProperties = new ArrayCollection();
    }

    /**
     * @return PriceMatrixPosition
     */
    public function getPosition(): PriceMatrixPosition
    {
        return $this->position;
    }
}