<?php

namespace Apto\Catalog\Domain\Core\Factory\ConfigurableProduct;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\RuleFactory;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\OrderedComputedProductValues;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\Repeatable;
use Apto\Catalog\Domain\Core\Model\Product\RepeatableValidationException;

class ConfigurableProduct implements \JsonSerializable
{
    /**
     * @var Product
     * @deprecated remove product by refactoring ComputedProductValueCalculator to value-objects
     */
    protected $product;

    /**
     * Lookup table for getting proper index from section uuids
     * @var array
     */
    protected $lookupSection = [];

    /**
     * Lookup table for getting proper index from section uuids
     * @var array
     */
    protected $lookupElement = [];

    /**
     * @var array
     */
    protected $data;

    /**
     * @var Rule[]
     */
    protected $rules;

    /**
     * @var OrderedComputedProductValues
     */
    protected $orderedComputedProductValues;

    /**
     * @var ElementDefinition[][]
     */
    protected $elementDefinitions;

    /**
     * @var bool
     */
    protected $cached;

    /**
     * @param array $configurableProduct
     * @param Product $product
     * @throws InvalidUuidException
     */
    public function __construct(array $configurableProduct, Product $product)
    {
        $this->product = $product;
        $this->data = $configurableProduct;

        // build lookup table Uuid->index
        foreach ($configurableProduct['sections'] as $sectionIndex => $section) {
            $sectionId = $section['id'];
            $this->lookupSection[$sectionId] = $sectionIndex;
            $this->lookupElement[$sectionId] = [];
            foreach ($section['elements'] as $elementIndex => $element) {
                $elementId = $element['id'];
                $this->lookupElement[$sectionId][$elementId] = $elementIndex;
            }
        }

        // build element definitions
        foreach ($configurableProduct['sections'] as $section) {
            $sectionId = $section['id'];
            $this->elementDefinitions[$sectionId] = [];
            foreach ($section['elements'] as $element) {
                $elementId = $element['id'];
                $this->elementDefinitions[$sectionId][$elementId] = $element['definitionObject'];
            }
        }

        // @todo still necessary?
        // set section mandatory
        foreach ($configurableProduct['sections'] as $section) {
            $sectionId = $section['id'];

            // continue if mandatory field is explicit set
            if ($section['isMandatory']) {
                continue;
            }

            // set section mandatory if one element is mandatory
            foreach ($section['elements'] as $element) {
                if ($element['isMandatory']) {
                    $this->data['sections'][$this->lookupSection[$sectionId]]['isMandatory'] = true;
                    break;
                }
            }
        }

        // parse rules
        unset($this->data['rules']);
        $this->rules = [];
        if (isset($configurableProduct['rules'])) {
            foreach ($configurableProduct['rules'] as $rawRule) {
                $this->rules[] = RuleFactory::fromArray($rawRule);
            }
        }

        // @todo refactor computer values to finder
        /*
        // parse computed values
        unset($this->data['computedValues']);
        $computedValues = [];
        if (isset($configurableProduct['computedValues'])) {
            $computedValues[] = ComputedProductValueFactory::fromArray(...);
        }
        $this->orderedComputedProductValues = OrderedComputedProductValues::fromArray($computedValues);
        */

        // is Cached?
        $this->cached = $configurableProduct['cached'];

    }

    /**
     * @return Product
     * @deprecated remove method by refactoring ComputedProductValueCalculator to value-objects
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return AptoUuid|null
     * @throws InvalidUuidException
     */
    public function getId(): ?AptoUuid
    {
        return new AptoUuid($this->data['id']);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->data['identifier'];
    }

    /**
     * @return array
     */
    public function getSections(): array
    {
        return $this->data['sections'] ?? [];
    }

    /**
     * @param AptoUuid $sectionId
     * @return array|null
     */
    public function getSection(AptoUuid $sectionId): ?array
    {
        $sectionIndex = $this->lookupSection[$sectionId->getId()] ?? null;
        if (null === $sectionIndex)
            return null;

        return $this->data['sections'][$sectionIndex] ?? null;
    }

    /**
     * @param AptoUuid $sectionId
     *
     * @return Repeatable|null
     * @throws RepeatableValidationException
     */
    public function getSectionRepeatableType(AptoUuid $sectionId): ?Repeatable
    {
        $section = $this->getSection($sectionId);

        if (is_null($section)) {
            return null;
        }

        if (!array_key_exists('repeatableType', $section)) {
            return new Repeatable(Repeatable::TYPES[0]);
        }

        return new Repeatable($section['repeatableType'], $section['repeatableCalculatedValueName']);
    }

    /**
     * @param AptoUuid $sectionId
     *
     * @return bool|null
     * @throws RepeatableValidationException
     */
    public function isSectionRepeatable(AptoUuid $sectionId): ?bool
    {
        $repeatable = $this->getSectionRepeatableType($sectionId);

        if(is_null($repeatable)) {
            return null;
        }

        return $repeatable->isRepeatable();
    }

    /**
     * @param AptoUuid    $sectionId
     * @param RulePayload $rulePayloadByName
     *
     * @return int|null
     * @throws RepeatableValidationException
     */
    public function getSectionRepetitionCount(AptoUuid $sectionId, RulePayload $rulePayloadByName): ?int
    {
        $section = $this->getSection($sectionId);
        $computedValues = $rulePayloadByName->getComputedValues();
        $repetitionCount = 1; // at least one item we should have repeated

        $repeatable = $this->getSectionRepeatableType($sectionId);

        if(is_null($repeatable)) {
            return 1;
        }

        if (array_key_exists('repeatableCalculatedValueName', $section)) {
            $repetitionCount = (int)$computedValues[$repeatable->getCalculatedValueName()];
        }

        return $repetitionCount === 0 ? 1 : $repetitionCount;
    }

    /**
     * @param AptoUuid $sectionId
     * @return bool
     */
    public function isSectionMultiple(AptoUuid $sectionId): bool
    {
        return $this->getSection($sectionId)['allowMultiple'] ?? false;
    }

    /**
     * @param AptoUuid $sectionId
     * @return string
     */
    public function getSectionIdentifier(AptoUuid $sectionId): string
    {
        $section = $this->getSection($sectionId);
        return $section ? $section['identifier'] : '';
    }

    /**
     * @param AptoUuid $sectionId
     * @return array
     * @throws InvalidUuidException
     */
    public function getElementIds(AptoUuid $sectionId): array
    {
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return [];
        }

        return array_map(
            function(string $id) { return new AptoUuid($id); },
            array_column($section['elements'], 'id')
        );
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return array|null
     */
    public function getElement(AptoUuid $sectionId, AptoUuid $elementId): ?array
    {
        $sectionIndex = $this->lookupSection[$sectionId->getId()] ?? null;
        if (null === $sectionIndex)
            return null;

        $elementIndex = $this->lookupElement[$sectionId->getId()][$elementId->getId()] ?? null;
        if (null === $elementIndex)
            return null;

        return $this->data['sections'][$sectionIndex]['elements'][$elementIndex] ?? null;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return string
     */
    public function getElementIdentifier(AptoUuid $sectionId, AptoUuid $elementId): string
    {
        $element = $this->getElement($sectionId, $elementId);
        return $element ? $element['identifier'] : '';
    }

    /**
     * Returns element definition set from backend
     *
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return ElementDefinition|null
     */
    public function getElementDefinition(AptoUuid $sectionId, AptoUuid $elementId): ?ElementDefinition
    {
        $section = $sectionId->getId();
        $element = $elementId->getId();

        return $this->elementDefinitions[$section][$element] ?? null;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @return ElementValueCollection|null
     */
    public function getElementValueCollection(AptoUuid $sectionId, AptoUuid $elementId, string $property): ?ElementValueCollection
    {
        $selectableValues = $this->getElementDefinition($sectionId, $elementId)->getSelectableValues();

        return $selectableValues[$property] ?? null;
    }

    /**
     * @param AptoUuid $sectionId
     * @return bool
     */
    public function hasSection(AptoUuid $sectionId): bool
    {
        $section = $sectionId->getId();

        return array_key_exists($section, $this->lookupSection);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return bool
     */
    public function hasElement(AptoUuid $sectionId, AptoUuid $elementId): bool
    {
        if (!$this->hasSection($sectionId)) {
            return false;
        }

        $section = $sectionId->getId();
        $element = $elementId->getId();

        return array_key_exists($element, $this->lookupElement[$section]);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @return bool
     */
    public function hasProperty(AptoUuid $sectionId, AptoUuid $elementId, string $property): bool
    {
        if (!$this->hasElement($sectionId, $elementId)) {
            return false;
        }

        $section = $sectionId->getId();
        $element = $elementId->getId();

        return array_key_exists($property, $this->data['sections'][$this->lookupSection[$section]]['elements'][$this->lookupElement[$section][$element]]['definition']['properties']);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @param $value
     * @return bool
     */
    public function hasValue(AptoUuid $sectionId, AptoUuid $elementId, string $property, $value): bool
    {
        if (!$this->hasProperty($sectionId, $elementId, $property)) {
            return false;
        }

        $valueCollection = $this->getElementValueCollection($sectionId, $elementId, $property);

        return null !== $valueCollection && $valueCollection->contains($value);
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return OrderedComputedProductValues
     */
    public function getOrderedComputedValues(): OrderedComputedProductValues
    {
        return $this->orderedComputedProductValues;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            $this->data, [
                'rules' => $this->rules
            ]
        );
    }

    /**
     * @return bool
     */
    public function isCached(): bool
    {
        return $this->cached;
    }

}
