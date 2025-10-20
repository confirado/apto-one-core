<?php

namespace Apto\Plugins\PdfGenerator\Domain\Core\Model\RandomNumber;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface RandomNumberRepository extends AptoRepository
{

    /**
     * @param RandomNumber $model
     */
    public function add(RandomNumber $model);

    /**
     * @param $number
     * @return RandomNumber|null
     */
    public function findByNumber($number);
}
