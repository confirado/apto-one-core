<?php

namespace Apto\Catalog\Application\Frontend\Query\Configuration;

use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Mpdf\Tag\A;
use Psr\Cache;
use Symfony\Component\Cache\Exception\CacheException;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\ConfigurableProduct\ConfigurableProductBuilder;
use Apto\Catalog\Application\Core\Query\Product\Element\RenderImageFactory;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Application\Core\Service\StatePrice\StatePriceService;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProductFactory;
use Apto\Catalog\Domain\Core\Factory\EnrichedState\EnrichedState;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayloadFactory;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionDefaultValues;
use Apto\Catalog\Domain\Core\Service\EnrichedStateValidation\RuleRepairService;
use Apto\Catalog\Domain\Core\Service\EnrichedStateValidation\RuleValidationService;
use Apto\Catalog\Domain\Core\Service\JavascriptStateCreatorService\JavascriptStateCreatorService;
use Apto\Catalog\Domain\Core\Service\StateValidation\InvalidStateException;
use Apto\Catalog\Domain\Core\Service\StateValidation\ValueValidationService;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

class ConfigurationStateQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ConfigurableProductFactory
     */
    private ConfigurableProductFactory $configurableProductFactory;

    /**
     * @var ValueValidationService
     */
    private ValueValidationService $valueValidationService;

    /**
     * @var RuleValidationService
     */
    private RuleValidationService $ruleValidationService;

    /**
     * @var RuleRepairService
     */
    private RuleRepairService $ruleRepairService;

    /**
     * @var RulePayloadFactory
     */
    private RulePayloadFactory $rulePayloadFactory;

    /**
     * @var JavascriptStateCreatorService
     */
    private JavascriptStateCreatorService $javaScriptStateCreatorService;

    /**
     * @var RenderImageFactory
     */
    protected RenderImageFactory $renderImageFactory;

    /**
     * @var ComputedProductValueCalculator
     */
    protected ComputedProductValueCalculator $computedProductValueCalculator;

    /**
     * @var StatePriceService
     */
    private StatePriceService $statePriceService;

    /**
     * @param ConfigurableProductBuilder $configurableProductBuilder
     * @param ProductRepository $productRepository
     * @param RuleValidationService $ruleValidationService
     * @param RulePayloadFactory $rulePayloadFactory
     * @param RenderImageFactory $renderImageFactory
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     * @param StatePriceService $statePriceService
     */
    public function __construct(
        ConfigurableProductBuilder $configurableProductBuilder,
        ProductRepository $productRepository,
        RuleValidationService $ruleValidationService,
        RulePayloadFactory $rulePayloadFactory,
        RenderImageFactory $renderImageFactory,
        ComputedProductValueCalculator $computedProductValueCalculator,
        StatePriceService $statePriceService
    ) {
        $this->configurableProductFactory = new ConfigurableProductFactory($configurableProductBuilder, $productRepository);
        $this->valueValidationService = new ValueValidationService();
        $this->ruleValidationService = $ruleValidationService;
        $this->ruleRepairService = new RuleRepairService($ruleValidationService);
        $this->javaScriptStateCreatorService = new JavascriptStateCreatorService($rulePayloadFactory);
        $this->rulePayloadFactory = $rulePayloadFactory;
        $this->renderImageFactory = $renderImageFactory;
        $this->computedProductValueCalculator = $computedProductValueCalculator;
        $this->statePriceService = $statePriceService;
    }

    /**
     * @param GetConfigurationState $query
     * @return array
     * @throws AptoJsonSerializerException
     * @throws CacheException
     * @throws Cache\InvalidArgumentException
     * @throws CircularReferenceException
     * @throws InvalidUuidException
     */
    public function handleGetConfigurationState(GetConfigurationState $query): array
    {
        $product = $this->configurableProductFactory->fromProductId($query->getProductId(), true, true);
        if ($product === null) {
            return [];
        }

        $enrichedState = new EnrichedState(
            new State($query->getState())
        );
        $maxTries = $query->getIntention()['repair']['maxTries'] ?? 0;
        $operatorsToFulfill = $query->getIntention()['repair']['operators'] ?? null;
        $selectEmptySections = $query->getIntention()['repair']['selectEmptySections'] ?? null;

        // init state
        if ($query->getIntention()['init'] ?? false) {
            try {
                // assert valid sections, elements, properties and values (if set)
                $this->valueValidationService->assertValidValues($product, $enrichedState->getState());

                $this->applyInit($product, $enrichedState);
            } catch (InvalidStateException $e) {
                throw InitConfigurationStateException::fromInvalidConfigurationStateException($e);
            }
        }

        // apply set/remove/complete actions
        try {
            if ($query->getIntention()['init'] ?? false) {
                $this->applyInit($product, $enrichedState);
            }

            $this->applySet($product, $enrichedState, $query->getIntention()['set'] ?? []);
            $this->applyRemove($product, $enrichedState, $query->getIntention()['remove'] ?? []);
            $this->applyComplete($product, $enrichedState, $query->getIntention()['complete'] ?? []);
        } catch (InvalidStateException $e) {
            throw InvalidConfigurationStateChangeException::fromInvalidConfigurationStateException($e);
        }

        // repair rules
        $validationResult = $this->ruleRepairService->repairState(
            $product,
            $enrichedState->getState(),
            $maxTries,
            $operatorsToFulfill
        );
        if ($selectEmptySections) {
            $validationResult = $this->ruleRepairService->repairStateAndSelectEmptySections(
                $product,
                $enrichedState,
                $validationResult,
                $maxTries,
                $operatorsToFulfill
            );
        }

        // throw exception on failed rules
        if ($validationResult->getFailed()) {
            // create failed rules as array representation
            $state = $enrichedState->getState();
            $rulePayload = $this->rulePayloadFactory->getPayload($product, $state);
            $failedRules = array_map(
                function($rule) use ($product, $state, $rulePayload) {
                    return $rule->toArray($product, $state, $rulePayload);
                },
                $validationResult->getFailed()
            );
            throw new FailedRulesException(
                sprintf(
                    'The state does not fulfill %s rules.',
                    count($failedRules)
                ),
                $failedRules
            );
        }

        // update disabled
        $enrichedState = $this->ruleValidationService->updateDisabled($product, $enrichedState, $validationResult);

        // assert valid sections, elements, properties and values (if set) again after state changes
        $this->valueValidationService->assertValidValues($product, $enrichedState->getState());

        // create js state result
        $stateResult = $this->javaScriptStateCreatorService->createState($product, $enrichedState, $validationResult, $query->getIntention());

        // apply render images
        $stateResult = $this->applyRenderImages($stateResult, $enrichedState->getState(), $product->getId());

        // apply perspectives
        $stateResult = $this->applyPerspectives($stateResult, $enrichedState->getState(), $product->getId());

        // apply computed values
        $stateResult = $this->applyComputedValues($stateResult, $enrichedState->getState(), $product->getId());

        // apply human readable state
        $stateResult = $this->applyHumanReadableState($stateResult, $enrichedState->getState(), $product->getProduct());

        if (null === $query->getConnector()) {
            return $stateResult;
        }

        // apply state price
        $stateResult = $this->applyStatePrice($stateResult, $enrichedState->getState(), $product->getId(), $query->getConnector(), $query->getUser());

        // return js state object
        return $stateResult;
    }

    /**
     * @param array $stateResult
     * @param State $state
     * @param AptoUuid $productId
     * @return array
     */
    private function applyStatePrice(array $stateResult, State $state, AptoUuid $productId, array $conenctor, ?array $user): array
    {
        $stateResult['statePrice'] = $this->statePriceService->getStatePrice(
            $productId,
            $state,
            new AptoLocale($conenctor['locale']),
            $conenctor['shopCurrency'],
            $conenctor['displayCurrency'],
            $conenctor['customerGroup']['id'],
            $conenctor['sessionCookies'],
            $conenctor['taxState']
        );
        return $stateResult;
    }

    /**
     * @param array $stateResult
     * @param State $state
     * @param Product $product
     * @return array
     * @throws InvalidUuidException
     */
    private function applyHumanReadableState(array $stateResult, State $state, Product $product): array
    {
        // init readable state
        $readableState = [];

        foreach ($state->getElementList() as $elementId => $elementValues) {
            // skip non array values, like false/true for DefaultElement
            if (!is_array($elementValues)) {
                continue;
            }
            $humanReadableValues = $this->getHumanReadableValues($product, new AptoUuid($elementId), $elementValues);
            if (null !== $humanReadableValues) {
                $readableState[$elementId] = $humanReadableValues;
            }
        }

        $stateResult['humanReadableState'] = $readableState;
        return $stateResult;
    }

    /**
     * @param Product $product
     * @param AptoUuid $elementId
     * @param array $elementValues
     * @return array
     * @throws InvalidUuidException
     */
    private function getHumanReadableValues(Product $product, AptoUuid $elementId, array $elementValues)
    {
        $elementDefinition = $this->getElementDefinitionByElementId($product, $elementId);
        if (null === $elementDefinition) {
            return [];
        }
        return $elementDefinition->getHumanReadableValues($elementValues);
    }

    /**
     * @param Product $product
     * @param AptoUuid $elementId
     * @return ElementDefinition|null
     * @throws InvalidUuidException
     */
    private function getElementDefinitionByElementId(Product $product, AptoUuid $elementId): ?ElementDefinition
    {
        foreach ($product->getSectionIds() as $sectionId) {
            return $product->getElementDefinition(new AptoUuid($sectionId), $elementId);
        }
        return null;
    }

    /**
     * @param array $stateResult
     * @param State $state
     * @param AptoUuid $productId
     * @return array
     */
    private function applyComputedValues(array $stateResult, State $state, AptoUuid $productId): array
    {
        $stateResult['computedValues'] = $this->computedProductValueCalculator->calculateComputedValues($productId->getId(), $state);
        return $stateResult;
    }

    /**
     * @param array $stateResult
     * @param State $state
     * @param AptoUuid $productId
     * @return array
     */
    private function applyPerspectives(array $stateResult, State $state, AptoUuid $productId): array
    {
        $stateResult['perspectives'] = $this->renderImageFactory->getPerspectivesByState($state, $productId->getId());
        return $stateResult;
    }

    /**
     * @param array $stateResult
     * @param State $state
     * @param AptoUuid $productId
     * @return array
     */
    private function applyRenderImages(array $stateResult, State $state, AptoUuid $productId): array
    {
        $stateResult['renderImages'] = $this->renderImageFactory->getRenderImagesByImageList($state, $productId->getId());
        return $stateResult;
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @return void
     * @throws InvalidUuidException
     */
    private function applyInit(ConfigurableProduct $product, EnrichedState $state)
    {
        $defaultElements = [];
        foreach ($product->getSections() as $section) {
            $sectionId = AptoUuid::fromId($section['id']);

            foreach ($section['elements'] as $element) {
                if (!$element['isDefault']) {
                    continue;
                }

                $elementId = AptoUuid::fromId($element['id']);

                $elementDefinition = $product->getElementDefinition($sectionId, $elementId);
                if ($elementDefinition instanceof ElementDefinitionDefaultValues) {
                    foreach ($elementDefinition->getDefaultValues() as $property => $value) {
                        $defaultElements[] = [
                            'sectionId' => $sectionId->getId(),
                            'elementId' => $elementId->getId(),
                            'property' => $property,
                            'value' => $value
                        ];
                    }
                } else {
                    $defaultElements[] = [
                        'sectionId' => $sectionId->getId(),
                        'elementId' => $elementId->getId()
                    ];
                }
            }
        }

        $this->applySet($product, $state, $defaultElements);
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param array $items
     * @throws InvalidUuidException
     */
    private function applySet(ConfigurableProduct $product, EnrichedState $state, array $items)
    {
        foreach ($items as $item) {
            $section = AptoUuid::fromId($item['sectionId']);
            $element = AptoUuid::fromId($item['elementId']);
            $property = $item['property'] ?? null;
            $value = $item['value'] ?? null;

            $this->valueValidationService->assertHasSection($product, $section);

            if ($element) {
                $this->valueValidationService->assertHasElement($product, $section, $element);
            }

            if ($property) {
                $this->valueValidationService->assertHasProperty($product, $section, $element, $property);
            }

            if (null !== $property) {
                $this->valueValidationService->assertHasValue($product, $section, $element, $property, $value);
            }

            // because we want to toggle elements on none multiple sections we remove the element section first in that case
            // the second condition is to prevent removing the section if an element needs to set multiple properties
            if ($product->isSectionMultiple($section) === false && $state->getState()->isElementActive($section, $element) === false) {
                $state->getState()->removeSection($section);
            }

            $state->getState()->setValue(
                $section,
                $element,
                $property,
                $value
            );
        }
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param array $items
     * @throws InvalidUuidException
     */
    private function applyRemove(ConfigurableProduct $product, EnrichedState $state, array $items)
    {
        foreach ($items as $item) {
            $section = AptoUuid::fromId($item['sectionId']);
            $element = AptoUuid::fromId($item['elementId'] ?? null);

            $this->valueValidationService->assertHasSection($product, $section);
            if ($element) {
                $this->valueValidationService->assertHasElement($product, $section, $element);
            }

            $state->getState()->removeValue(
                $section,
                $element
            );
        }
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param array $items
     * @throws InvalidUuidException
     */
    private function applyComplete(ConfigurableProduct $product, EnrichedState $state, array $items)
    {
        foreach ($items as $item) {
            $section = AptoUuid::fromId($item['sectionId']);
            $this->valueValidationService->assertHasSection($product, $section);
            $state->setSectionComplete(
                $section,
                boolval($item['complete'] ?? true)
            );
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield GetConfigurationState::class => [
            'method' => 'handleGetConfigurationState',
            'bus' => 'query_bus'
        ];
    }
}
