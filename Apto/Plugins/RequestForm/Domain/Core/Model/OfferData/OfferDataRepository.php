<?php

namespace Apto\Plugins\RequestForm\Domain\Core\Model\OfferData;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface OfferDataRepository extends AptoRepository
{

    /**
     * @param OfferData $model
     */
    public function add(OfferData $model);
}
