<?php

namespace Apto\Plugins\AreaElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\AbstractSpecialElementTranslationExportProvider;
use Apto\Plugins\AreaElement\Domain\Core\Model\Product\Element\AreaElementDefinition;

class AreaElementTranslationExportProvider extends AbstractSpecialElementTranslationExportProvider
{
    const TRANSLATABLE_FIELDS = ['livePricePrefix', 'livePriceSuffix'];
    const TRANSLATION_TYPE = 'AreaElement';
    const DEFINITION_CLASS = AreaElementDefinition::class;

    public function getTranslatedValues(): array
    {
        $specialElements = [];
        $products = $this->productFinder->findTranslatableProductFields();
        foreach ($products['data'] as $product) {
            $specialElements = array_merge($specialElements,$this->getAllSpecialElements($product));
        }
        $translationItems = [];
        foreach ($specialElements as $specialElement) {
            foreach ($specialElement['definition']['json']['fields'] as $key => $field) {
                $translationItems[] = $this->makeTranslationItem($specialElement['translationIdent'] . $key . '_prefix', $field['prefix'], $specialElement['id']);
                $translationItems[] = $this->makeTranslationItem($specialElement['translationIdent'] . $key . '_suffix', $field['suffix'], $specialElement['id']);
            }
            foreach ($this->translatableFields as $translatableField) {
                if (array_key_exists($translatableField, $specialElement['definition']['json'])) {
                    $translationItems[] = $this->makeTranslationItem($specialElement['translationIdent'] . $translatableField, $specialElement['definition']['json'][$translatableField], $specialElement['id']);
                }
            }
        }
        return $translationItems;
    }

}