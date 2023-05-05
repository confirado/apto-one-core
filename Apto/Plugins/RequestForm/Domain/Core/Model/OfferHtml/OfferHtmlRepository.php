<?php

namespace Apto\Plugins\RequestForm\Domain\Core\Model\OfferHtml;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface OfferHtmlRepository extends AptoRepository
{

    /**
     * @param OfferHtml $model
     */
    public function add(OfferHtml $model);
}