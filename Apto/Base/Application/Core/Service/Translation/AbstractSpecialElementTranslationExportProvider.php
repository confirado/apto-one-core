<?php

namespace Apto\Base\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\DefinitionClassNotFoundFoundException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;

abstract class AbstractSpecialElementTranslationExportProvider extends AbstractTranslationExportProvider
{
    const TRANSLATABLE_FIELDS = [];
    const DEFINITION_CLASS = 'SET_DEFINITION_CLASS_IN_CHILD_CLASS';

    /**
     * @var array
     */
    protected $translatableFields;

    /**
     * @var string
     */
    protected $definitionClass;

    /**
     * @var ProductFinder
     */
    protected $productFinder;

    /**
     * @param ProductFinder $productFinder
     * @throws DefinitionClassNotFoundFoundException
     * @throws Exceptions\TranslationTypeNotFoundException
     */
    public function __construct(ProductFinder $productFinder) {
        parent::__construct();
        $this->translatableFields = static::TRANSLATABLE_FIELDS;
        $this->definitionClass = static::DEFINITION_CLASS;
        $this->productFinder = $productFinder;

        if ($this->definitionClass === self::DEFINITION_CLASS) {
            throw new DefinitionClassNotFoundFoundException('DefinitionClass not set in child class.');
        }
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getTranslatedValues(): array
    {
        $specialElements = [];
        $products = $this->productFinder->findTranslatableProductFields();
        foreach ($products['data'] as $product) {
            $specialElements = array_merge($specialElements,$this->getAllSpecialElements($product));
        }
        $translationItems = [];
        foreach ($specialElements as $specialElement) {
            foreach ($this->translatableFields as $translatableField) {
                if (array_key_exists($translatableField, $specialElement['definition']['json'])) {
                    $translationItems[] = $this->makeTranslationItem($specialElement['translationIdent'] . $translatableField, $specialElement['definition']['json'][$translatableField], $specialElement['id']);
                }
            }
        }
        return $translationItems;
    }

    /**
     * @param array $product
     * @return array
     */
    protected function getAllSpecialElements(array $product)
    {
        $specialElements = [];
        $sectionsElements = $this->productFinder->findSectionsElements($product['id']);
        foreach ($sectionsElements['sections'] as $section) {
            foreach ($section['elements'] as $element) {
                if ($element['definition'] = $this->getSpecialElement($element)) {
                    $element['translationIdent'] = $product['identifier'] . '_' . $section['identifier'] . '_' . $element['identifier'] . '_';
                    $specialElements[] = $element;
                }
            }
        }
        return $specialElements;
    }

    /**
     * @param $element
     * @return bool|mixed
     */
    protected function getSpecialElement($element)
    {
        $elementDefinitionArray = json_decode($element['definition'], true);
        if ($elementDefinitionArray['class'] === $this->definitionClass) {
            return $elementDefinitionArray;
        }

        return false;
    }
}