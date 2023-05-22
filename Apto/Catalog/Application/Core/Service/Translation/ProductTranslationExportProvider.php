<?php

namespace Apto\Catalog\Application\Core\Service\Translation;

use _PHPStan_a3459023a\Nette\Neon\Exception;
use Apto\Base\Application\Core\Query\AptoFinder;
use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslationTypeNotFoundException;
use Apto\Base\Application\Core\Service\Translation\TranslationItem;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Query\Product\Element\ProductElementFinder;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Base\Application\Core\Service\Translation\AbstractTranslationExportProvider;
use Apto\Catalog\Application\Core\Query\Product\Section\ProductSectionFinder;

class ProductTranslationExportProvider extends AbstractTranslationExportProvider
{
    const TRANSLATION_TYPE = 'Product';

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * @var ProductSectionFinder
     */
    private $sectionFinder;

    /**
     * @var ProductElementFinder
     */
    private $elementFinder;

    /**
     * @param ProductFinder $productFinder
     * @param ProductSectionFinder $sectionFinder
     * @param ProductElementFinder $elementFinder
     * @throws TranslationTypeNotFoundException
     */
    public function __construct(ProductFinder $productFinder, ProductSectionFinder $sectionFinder, ProductElementFinder $elementFinder)
    {
        parent::__construct();
        $this->productFinder = $productFinder;
        $this->sectionFinder = $sectionFinder;
        $this->elementFinder = $elementFinder;
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getTranslatedValues(): array
    {
        $translatableProducts = [];
        $products = $this->productFinder->findTranslatableProductFields();
        foreach ($products['data'] as $product) {
            $translatableProducts[] = $this->getAllProductFields($product);
        }

        $translationItems= [];
        foreach ($translatableProducts as $product) {
            $translationItems = array_merge($translationItems, $this->makeProductTranslationItems($product));
        }

        return $translationItems;

    }

    /**
     * @param array $product
     * @return array
     * @throws InvalidUuidException
     */
    private function makeProductTranslationItems(array $product) {
        $productPrefix = $product['identifier'];

        // Product
        $fields = [
            'name' => $product['name'],
            'description' => $product['description'],
            'metaTitle' => $product['metaTitle'],
            'metaDescription' => $product['metaDescription']
        ];
        $nestedFields = [
            'customProperties' => $product['customProperties'],
            'rules' => $product['rules'],
            'discounts' => $product['discounts']
        ];
        $result = $this->getEntitiesTranslationItems($fields, $productPrefix, $product['id'], $nestedFields);

        // Sections
        foreach ($product['sections'] as $section) {
            $result = array_merge($result, $this->makeSectionTranslationItems($section, $product));
        }

        return $result;
    }

    /**
     * @param array $section
     * @param array $product
     * @return array
     * @throws InvalidUuidException
     */
    private function makeSectionTranslationItems(array $section, array $product)
    {
        $sectionPrefix = $product['identifier'] . '_PS_'. $section['identifier'];
        $fields = [
            'name' => $section['name'],
            'description' => $section['description'],
        ];
        $nestedFields = [
            'customProperties' => $section['customProperties'],
            'discounts' => $section['discounts']
        ];
        $result = $this->getEntitiesTranslationItems($fields, $sectionPrefix, $product['id'], $nestedFields);

        // Elements
        foreach ($section['elements'] as $element) {
            $result = array_merge($result, $this->makeElementTranslationItems($element,$section, $product));
        }
        return $result;
    }

    /**
     * @param array $element
     * @param array $section
     * @param array $product
     * @return array
     * @throws InvalidUuidException
     */
    private function makeElementTranslationItems(array $element, array $section, array $product)
    {
        $elementPrefix = $product['identifier'] . '_PSE_'. $section['identifier'] . '_' . $element['identifier'];
        $fields = [
            'name' => $element['name'],
            'description' => $element['description'],
            'errorMessage' => $element['errorMessage']
        ];
        $nestedFields = [
            'customProperties' => $element['customProperties'],
            'discounts' => $element['discounts']
        ];
        return $this->getEntitiesTranslationItems($fields, $elementPrefix, $product['id'], $nestedFields);
    }

    /**
     * @param array $fields
     * @param string $prefix
     * @param string $id
     * @param array $nestedFields
     * @return array
     * @throws InvalidUuidException
     */
    function getEntitiesTranslationItems(array $fields, string $prefix, string $id, array $nestedFields)
    {
        $translationItems = [];
        foreach ($fields as $fieldName => $fieldValue) {
            if ($fieldValue === null) {
                $fieldValue = [];
            }
            $translationItems[] = $this->getTranslationItem($prefix, $fieldName, $fieldValue, $id);
        }
        if (key_exists('customProperties', $nestedFields)) {
            foreach ($nestedFields['customProperties'] as $customProperty) {
                $translationItems[] = $this->getTranslationItem($prefix, 'customProperty_' . $customProperty['key'],  $customProperty['value'], $id);
            }

        }
        if (key_exists('discounts', $nestedFields)) {
            $i = 1;
            foreach ($nestedFields['discounts'] as $discount) {
                $translationItems[] = $this->getTranslationItem($prefix, 'discount_' . $i,  $discount['name'], $discount['id']);
                $i++;
            }
        }
        if (key_exists('rules', $nestedFields)) {
            // Rules
            foreach ($nestedFields['rules'] as $rule) {
                $translationItems[] = $this->getTranslationItem($prefix, 'rule_' . $rule['name'], $rule['errorMessage'], $rule['id']);
            }
        }
        return $translationItems;
    }

    /**
     * @param array $product
     * @return array
     */
    private function getAllProductFields(array $product)
    {

        $sectionsElements = $this->productFinder->findTranslatableSectionsElements($product['id'])['sections'];
        $parsedSections = [];

        // get Sections & Elements - SubEntities (CustomProperties,...)
        foreach ($sectionsElements as $section )
        {
            $parsedSections[] = $this->getSectionElementFields($section);
        }

        $result = [
            'id' => $product['id'],
            'identifier' => $product['identifier'],
            'name' => $product['name'],
            'description' => $product['description'],
            'metaTitle' => $product['metaTitle'],
            'metaDescription' => $product['metaDescription'],
            'sections' => $parsedSections
        ];

        // get other nested entities (Rules, Discounts, CustomProperties, etc...)
        $nestedFields = $this->getNestedFields($this->productFinder, $product['id'], true);
        return array_merge($result, $nestedFields);
    }

    /**
     * @param array $section
     * @return array
     */
    private function getSectionElementFields(array $section)
    {
        foreach ($section['elements'] as &$element) {
            $nestedFields = $this->getNestedFields($this->elementFinder, $element['id']);
            $element = array_merge($element, $nestedFields);
        }

        // get other nested entities (Rules, Discounts, CustomProperties, etc...)
        $nestedFields = $this->getNestedFields($this->sectionFinder, $section['id']);

        return  array_merge($section, $nestedFields);
    }

    /**
     * @param AptoFinder $finder
     * @param string $entityId
     * @param bool $rules
     * @return array
     */
    private function getNestedFields(AptoFinder $finder, string $entityId, bool $rules = false)
    {
        $result = [];

        if ($rules && $finder instanceof ProductFinder) {
            // get Rules
            $result['rules'] = $this->getRules($finder, $entityId);
        }

        // get Discounts
        $result['discounts'] = $this->getDiscounts($finder, $entityId);

        // get Custom Properties
        $result['customProperties'] = $this->getCustomProperties($finder, $entityId);

        return $result;
    }

    /**
     * @param ProductFinder $finder
     * @param string $entityId
     * @return mixed
     */
    private function getRules(ProductFinder $finder, string $entityId)
    {
        return $finder->findRules($entityId)['rules'];
    }

    /**
     * @param AptoFinder $finder
     * @param string $entityId
     * @return mixed
     */
    private function getDiscounts(AptoFinder $finder, string $entityId)
    {
        if ($finder instanceof ProductFinder || $finder instanceof ProductSectionFinder || $finder instanceof ProductElementFinder) {
            return $finder->findDiscounts($entityId);
        }

        throw new Exception('Finder must be an instance of "ProductFinder", "ProductSectionFinder" or "ProductElementFinder".');
    }

    /**
     * @param AptoFinder $finder
     * @param string $entityId
     * @return array
     */
    private function getCustomProperties(AptoFinder $finder, string $entityId)
    {
        if ($finder instanceof ProductFinder || $finder instanceof ProductSectionFinder || $finder instanceof ProductElementFinder) {
            $customProperties = $finder->findCustomProperties($entityId)['customProperties'];
            return $this->filterCustomProperties($customProperties);
        }

        throw new Exception('Finder must be an instance of "ProductFinder", "ProductSectionFinder" or "ProductElementFinder".');
    }

    /**
     * @param array $customProperties
     * @return array
     */
    private function filterCustomProperties(array $customProperties)
    {
        $filteredResult = [];
        foreach ($customProperties as $customProperty) {
            if (array_key_exists('translatable', $customProperty) && $customProperty['translatable']) {
                $filteredResult[] = $customProperty;
            }
        }
        return $filteredResult;
    }

    /**
     * @param string $prefix
     * @param string $field
     * @param array $value
     * @param string $id
     * @return TranslationItem
     * @throws InvalidUuidException
     */
    private function getTranslationItem(string $prefix, string $field, array $value, string $id)
    {
        return $this->makeTranslationItem($prefix . '_'. $field, $value, $id);
    }
}
