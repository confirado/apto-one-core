<?php

namespace Apto\Catalog\Domain\Core\Service\JavascriptStateCreatorService;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Query\Product\Element\RenderImageFactory;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\EnrichedState\EnrichedState;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayloadFactory;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\RepeatableValidationException;
use Apto\Catalog\Domain\Core\Service\EnrichedStateValidation\RuleValidationResult;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;

class JavascriptStateCreatorService
{
    /**
     * @var RulePayloadFactory
     */
    protected RulePayloadFactory $rulePayloadFactory;

    /**
     * @var RenderImageFactory
     */
    protected RenderImageFactory $renderImageFactory;

    /**
     * @param RulePayloadFactory $rulePayloadFactory
     * @param RenderImageFactory $renderImageFactory
     */
    public function __construct(RulePayloadFactory $rulePayloadFactory, RenderImageFactory $renderImageFactory)
    {
        $this->rulePayloadFactory = $rulePayloadFactory;
        $this->renderImageFactory = $renderImageFactory;
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param RuleValidationResult $ruleValidationResult
     * @param array $intention
     * @return array
     * @throws CircularReferenceException
     * @throws InvalidUuidException
     * @throws RepeatableValidationException
     */
    public function createState(ConfigurableProduct $product, EnrichedState $state, RuleValidationResult $ruleValidationResult, array $intention): array
    {
        $rulePayloadById = $this->rulePayloadFactory->getPayload($product, $state->getState());
        $rulePayloadByName = $this->rulePayloadFactory->getPayload($product, $state->getState(), false);

        $appEnv = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'prod';
        $result = [
            'configurationState' => [
                'sections' => $this->createSections($product, $state, $rulePayloadById, $rulePayloadByName),
                'elements' => $this->createElements($product, $state, $rulePayloadById, $rulePayloadByName)
            ],
            'compressedState' => $state->getState()->jsonSerialize(),
            'failedRules' => $this->createFailedRules($product, $state, $ruleValidationResult, $rulePayloadById),
            'ignoredRules' => $this->createIgnoredRules($product, $state, $ruleValidationResult, $rulePayloadById),
            'cachedProduct' => $product->isCached(),
            'intention' => $intention,
            'renderImages' => $this->renderImageFactory->getRenderImagesByImageList($state->getState(), $product->getId()->getId())
        ];

        if ($appEnv === 'dev') {
            $result['allRules'] = $this->createAllRules($product, $state, $ruleValidationResult, $rulePayloadById);
        }
        return $result;
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $enrichedState
     * @param RulePayload $rulePayloadById
     * @param RulePayload $rulePayloadByName
     * @return array
     * @throws InvalidUuidException
     * @throws RepeatableValidationException
     */
    private function createSections(ConfigurableProduct $product, EnrichedState $enrichedState, RulePayload $rulePayloadById, RulePayload $rulePayloadByName): array
    {
        $state = $enrichedState->getState();
        $sections = [];

        foreach ($product->getSections() as $section) {
            $sectionId = new AptoUuid($section['id']);
            $elementIds = $product->getElementIds($sectionId);

            $sectionCount = 1;
            if ($product->isSectionRepeatable($sectionId)) {
                $sectionCount = $product->getSectionRepetitionCount($sectionId, $rulePayloadByName);
            }

            for($repetition = 0; $repetition < $sectionCount; $repetition++) {
                $sections[] = [
                    'id' => $sectionId->getId(),
                    'repetition' => $repetition,
                    'allowMultiple' => $section['allowMultiple'],
                    //'defaultElements' => [], // @TODO shall we implement defaultElements, just use isDefault flag from elements instead?!
                    'identifier' => $section['identifier'],
                    'isHidden' => $section['isHidden'],
                    'isMandatory' => $section['isMandatory'],
                    'repeatableType' => $section['repeatableType'],
                    'repeatableCalculatedValueName' => $section['repeatableCalculatedValueName'],
                    'name' => AptoTranslatedValue::fromArray($section['name'] ?: []),
                    'state' => [
                        'active' => $state->isSectionActive($sectionId, $repetition),
                        'complete' => $enrichedState->isSectionComplete($sectionId, $elementIds, $section['allowMultiple'], $section['isMandatory'], $repetition),
                        'disabled' => $enrichedState->isSectionDisabled($sectionId, $elementIds, $repetition)
                    ],
                    'customProperties' => $this->filterCustomPropertiesByConditionSet($product, $state, $rulePayloadById, $section['customProperties'])
                ];
            }
        }

        return $sections;
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $enrichedState
     * @param RulePayload $rulePayloadById
     * @param RulePayload $rulePayloadByName
     * @return array
     * @throws InvalidUuidException
     * @throws RepeatableValidationException
     */
    private function createElements(ConfigurableProduct $product, EnrichedState $enrichedState, RulePayload $rulePayloadById, RulePayload $rulePayloadByName): array
    {
        $state = $enrichedState->getState();
        $elements = [];

        foreach ($product->getSections() as $section) {
            $sectionId = new AptoUuid($section['id']);

            $sectionCount = 1;
            if ($product->isSectionRepeatable($sectionId)) {
                $sectionCount = $product->getSectionRepetitionCount($sectionId, $rulePayloadByName);
            }

            for($repetition = 0; $repetition < $sectionCount; $repetition++) {
                foreach ($section['elements'] as $element) {
                    $elementId = new AptoUuid($element['id']);

                    // empty properties must be initialized with null, elements without electable values must use null instead of an empty array
                    $selectedValues = array_merge(
                        array_fill_keys(
                            array_keys(
                                $element['definition']['properties'] ?? []
                            ),
                            null
                        ),
                        $state->getValues($sectionId, $elementId, $repetition) ?: []
                    ) ?: null;

                    $elements[] = [
                        'id' => $elementId->getId(),
                        'sectionId' => $sectionId->getId(),
                        'sectionRepetition' => $repetition,
                        'errorMessage' => AptoTranslatedValue::fromArray($element['errorMessage'] ?: []),
                        'identifier' => $element['identifier'],
                        'isDefault' => $element['isDefault'],
                        'isMandatory' => $element['isMandatory'],
                        'name' => AptoTranslatedValue::fromArray($element['name'] ?: []),
                        'previewImage' => $element['previewImage'],
                        'properties' => $element['definition']['properties'] ?? null, // @TODO needed anymore?
                        'state' => [
                            'active' => $state->isElementActive($sectionId, $elementId, $repetition),
                            'disabled' => $enrichedState->isElementDisabled($sectionId, $elementId, $repetition),
                            'values' => $selectedValues
                        ],
                        'staticValues' => $element['definition']['staticValues'] ?? [],
                        'customProperties' => $this->filterCustomPropertiesByConditionSet($product, $state, $rulePayloadById, $element['customProperties'])
                    ];
                }
            }
        }

        return $elements;
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param RuleValidationResult $validationResult
     * @param RulePayload $rulePayloadById
     * @return array
     */
    private function createFailedRules(ConfigurableProduct $product, EnrichedState $state, RuleValidationResult $validationResult, RulePayload $rulePayloadById): array
    {
        return $this->convertRulesToArray(
            $product,
            $state,
            $rulePayloadById,
            $validationResult->getFailed()
        );
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param RuleValidationResult $validationResult
     * @param RulePayload $rulePayloadById
     * @return array
     */
    private function createIgnoredRules(ConfigurableProduct $product, EnrichedState $state, RuleValidationResult $validationResult, RulePayload $rulePayloadById): array
    {
        return $this->convertRulesToArray(
            $product,
            $state,
            $rulePayloadById,
            $validationResult->getIgnored()
        );
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param RuleValidationResult $validationResult
     * @param RulePayload $rulePayloadById
     * @return array
     */
    private function createAllRules(ConfigurableProduct $product, EnrichedState $state, RuleValidationResult $validationResult, RulePayload $rulePayloadById): array
    {
        return $this->convertRulesToArray(
            $product,
            $state,
            $rulePayloadById,
            array_merge($validationResult->getAffected(), $validationResult->getInactive())
        );
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $enrichedState
     * @param RulePayload $rulePayloadById
     * @param array $rules
     * @return array
     */
    private function convertRulesToArray(ConfigurableProduct $product, EnrichedState $enrichedState, RulePayload $rulePayloadById, array $rules): array
    {
        $state = $enrichedState->getState();

        return array_map(
            function(Rule $rule) use ($product, $state, $rulePayloadById) {
                return $rule->toArray($product, $state, $rulePayloadById);
            },
            $rules
        );
    }

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @param RulePayload $rulePayloadById
     * @param array $customProperties
     * @return array
     */
    private function filterCustomPropertiesByConditionSet(ConfigurableProduct $product, State $state, RulePayload $rulePayloadById, array $customProperties): array
    {
        $filtered = [];

        foreach ($customProperties as $customProperty) {
            $key = $customProperty['key'];
            if (empty($customProperty['productConditionId'])) {
                // if custom property is already set, a condition was already true so custom property must not be overwritten!
                if (!array_key_exists($key, $filtered)) {
                    $filtered[$key] = $customProperty;
                }
                continue;
            }

            $conditionSet = $product->getConditionSetById($customProperty['productConditionId']);
            if ($conditionSet?->isFulfilled($state, $rulePayloadById)) {
                $filtered[$key] = $customProperty;
            }
        }

        return array_values($filtered);
    }
}
