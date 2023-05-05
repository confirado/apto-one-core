<?php

namespace Apto\Catalog\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslationTypeNotFoundException;
use Apto\Catalog\Application\Core\Query\PriceMatrix\PriceMatrixFinder;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Application\Core\Service\Translation\AbstractTranslationExportProvider;

class PriceMatrixTranslationExportProvider extends AbstractTranslationExportProvider
{
    const TRANSLATION_TYPE = 'PriceMatrix';

    /**
     * @var PriceMatrixFinder
     */
    private $priceMatrixFinder;

    /**
     * @param PriceMatrixFinder $priceMatrixFinder
     * @throws TranslationTypeNotFoundException
     */
    public function __construct(PriceMatrixFinder $priceMatrixFinder)
    {
        parent::__construct();
        $this->priceMatrixFinder = $priceMatrixFinder;
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getTranslatedValues(): array
    {
        $translatedValues = $this->priceMatrixFinder->findPriceMatrices('');
        $translatedValueImportExportObjects = [];
        $i = 1;
        foreach ($translatedValues['data'] as $translatedValue) {
            $ident = 'PriceMatrix#' . $i;
            $translatedValueImportExportObjects[] = $this->makeTranslationItem($ident, $translatedValue['name'], $translatedValue['id']);
            $i++;
        }
        return $translatedValueImportExportObjects;
    }
}