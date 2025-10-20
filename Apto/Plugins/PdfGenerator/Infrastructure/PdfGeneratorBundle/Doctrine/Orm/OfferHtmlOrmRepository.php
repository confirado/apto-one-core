<?php

namespace Apto\Plugins\PdfGenerator\Infrastructure\PdfGeneratorBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\OfferHtml\OfferHtml;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\OfferHtml\OfferHtmlRepository;

class OfferHtmlOrmRepository extends AptoOrmRepository implements OfferHtmlRepository
{
    const ENTITY_CLASS = OfferHtml::class;

    /**
     * @param OfferHtml $model
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(OfferHtml $model)
    {
        $this->_em->persist($model);
    }
}
