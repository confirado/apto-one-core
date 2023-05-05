<?php

namespace Apto\Plugins\SelectBoxElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\AbstractSpecialElementTranslationExportProvider;
use Apto\Base\Application\Core\Service\Translation\Exceptions\DefinitionClassNotFoundFoundException;
use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslationTypeNotFoundException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Plugins\SelectBoxElement\Application\Core\Query\SelectBoxItem\SelectBoxItemFinder;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\Product\Element\SelectBoxElementDefinition;

class SelectBoxElementTranslationExportProvider extends AbstractSpecialElementTranslationExportProvider
{
    const TRANSLATABLE_FIELDS = ['textBoxPrefix', 'textBoxSuffix', 'livePricePrefix', 'livePriceSuffix'];
    const TRANSLATION_TYPE = 'SelectBoxElement';
    const DEFINITION_CLASS = SelectBoxElementDefinition::class;

    /**
     * @var SelectBoxItemFinder
     */
    private $selectBoxItemFinder;

    /**
     * @param ProductFinder $productFinder
     * @param SelectBoxItemFinder $selectBoxItemFinder
     * @throws DefinitionClassNotFoundFoundException
     * @throws TranslationTypeNotFoundException
     */
    public function __construct(ProductFinder $productFinder, SelectBoxItemFinder $selectBoxItemFinder)
    {
        parent::__construct($productFinder);
        $this->selectBoxItemFinder = $selectBoxItemFinder;
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getTranslatedValues(): array
    {
        $selectBoxItems = [];
        $products = $this->productFinder->findTranslatableProductFields();
        foreach ($products['data'] as $product) {
            $selectBoxItems = array_merge($selectBoxItems,$this->getAllSpecialElements($product));
        }

        $translationItems = [];
        foreach ($selectBoxItems as $selectBoxItem) {
            $translationItems[] = $this->makeTranslationItem($selectBoxItem['translationIdent'], $selectBoxItem['name'], $selectBoxItem['id']);
        }
        return $translationItems;
    }

    /**
     * @param array $product
     * @return array
     */
    protected function getAllSpecialElements(array $product)
    {
        $selectBoxElements = parent::getAllSpecialElements($product);
        $selectBoxItems = [];
        foreach ($selectBoxElements as $selectBoxElement) {
            $selectBoxItems = array_merge($selectBoxItems, $this->selectBoxItemFinder->findByElementId($selectBoxElement['id'])['data']);
            foreach ($selectBoxItems as $key => $selectBoxItem) {
                $selectBoxItems[$key]['translationIdent'] = $selectBoxElement['translationIdent'] . $key;
            }
        }
        return $selectBoxItems;
    }
}