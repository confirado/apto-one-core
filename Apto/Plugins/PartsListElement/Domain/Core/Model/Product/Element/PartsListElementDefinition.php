<?php

namespace Apto\Plugins\PartsListElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementJsonValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementSingleTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;

class PartsListElementDefinition implements ElementDefinition
{
    const NAME = 'Parts List Element';
    const BACKEND_COMPONENT = '<parts-list-element-definition definition-validation="setDefinitionValidation(definitionValidation)"></parts-list-element-definition>';
    const FRONTEND_COMPONENT = '';

    /**
     * @var string|null
     */
    protected ?string $category;

    /**
     * @var bool
     */
    protected bool $allowMultiple;

    /**
     * PartsListElementDefinition constructor.
     */
    public function __construct(?string $category, bool $allowMultiple)
    {
        $this->category = $category;
        $this->allowMultiple = $allowMultiple;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        return [
            'aptoElementDefinitionId' => new ElementValueCollection([new ElementSingleTextValue('apto-parts-list-element')]),
            'selectedItems' => new ElementValueCollection([new ElementJsonValue()])
        ];
    }

    /**
     * @param array $selectedValues
     * @return mixed|null
     */
    public function getComputableValues(array $selectedValues): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getStaticValues(): array
    {
        return [
            'aptoElementDefinitionId' => 'apto-parts-list-element',
            'category' => $this->category,
            'allowMultiple' => $this->allowMultiple,
        ];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        $humanReadableValues = [];

        foreach ($selectedValues['selectedItems'] as $item) {
            $humanReadableValues[$item['id']] = AptoTranslatedValue::fromArray([
                'de_DE' => $item['partName'],
            ]);
        }

        return $humanReadableValues;
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public static function getBackendComponent(): string
    {
        return self::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public static function getFrontendComponent(): string
    {
        return self::FRONTEND_COMPONENT;
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
            'json' => [
                'category' => $this->category,
                'allowMultiple' => $this->allowMultiple,
            ]
        ];
    }

    /**
     * @param array $json
     * @return ElementDefinition
     */
    public static function jsonDecode(array $json): ElementDefinition
    {
        if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'PartsListElementDefinition\' due to wrong class namespace.');
        }

        if (!array_key_exists('category', $json['json'])) {
            $json['json']['category'] = null;
        }

        if (!array_key_exists('allowMultiple', $json['json'])) {
            $json['json']['allowMultiple'] = false;
        }

        return new self(
            $json['json']['category'],
            $json['json']['allowMultiple']
        );
    }
}
