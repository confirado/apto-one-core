<?php

namespace Apto\Base\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Query\Language\LanguageFinder;
use Apto\Base\Domain\Core\Model\InvalidUuidException;

class LanguageTranslationExportProvider extends AbstractTranslationExportProvider
{
    const TRANSLATION_TYPE = 'Language';

    /**
     * @var LanguageFinder
     */
    private $languageFinder;

    /**
     * @param LanguageFinder $languageFinder
     * @throws Exceptions\TranslationTypeNotFoundException
     */
    public function __construct(LanguageFinder $languageFinder)
    {
        parent::__construct();
        $this->languageFinder = $languageFinder;
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getTranslatedValues(): array
    {
        $translatedValues = $this->languageFinder->findTranslatedValues();
        $translatedValueImportExportObjects = [];
        foreach ($translatedValues['data'] as $translatedValue) {
            $translatedValueImportExportObjects[] = $this->makeTranslationItem($translatedValue['isocode'], $translatedValue['name'], $translatedValue['id']);
        }
        return $translatedValueImportExportObjects;
    }
}