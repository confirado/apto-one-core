<?php

namespace Apto\Catalog\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslationTypeNotFoundException;
use Apto\Catalog\Application\Core\Query\Category\CategoryFinder;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Application\Core\Service\Translation\AbstractTranslationExportProvider;

class CategoryTranslationExportProvider extends AbstractTranslationExportProvider
{
    const TRANSLATION_TYPE = 'Category';

    /**
     * @var CategoryFinder
     */
    private $categoryFinder;

    /**
     * @param CategoryFinder $categoryFinder
     * @throws TranslationTypeNotFoundException
     */
    public function __construct(CategoryFinder $categoryFinder)
    {
        parent::__construct();
        $this->categoryFinder = $categoryFinder;
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getTranslatedValues(): array
    {
        $translatedValues = $this->categoryFinder->findCategories();
        $translatedValueImportExportObjects = [];
        $i = 1;
        foreach ($translatedValues['data'] as $translatedValue) {
            $ident = 'Category#' . $i . '_';
            $translatedValueImportExportObjects[] = $this->makeTranslationItem($ident . 'name', $translatedValue['name'], $translatedValue['id']);
            $translatedValueImportExportObjects[] = $this->makeTranslationItem($ident . 'description', $translatedValue['description'], $translatedValue['id']);
            $i++;
        }
        return $translatedValueImportExportObjects;
    }
}