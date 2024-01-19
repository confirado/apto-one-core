<?php

namespace Apto\Catalog\Application\Frontend\Query\Configuration;

use Apto\Catalog\Application\Core\Query\Product\Element\RenderImageFactory;
use Apto\Catalog\Domain\Core\Model\Product\RepeatableValidationException;
use Psr\Cache;
use Symfony\Component\Cache\Exception\CacheException;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\ConfigurableProduct\ConfigurableProductBuilder;
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
use Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm\ProductOrmRepository;

class ConfigurationStateQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ConfigurableProductFactory
     */
    private $configurableProductFactory;

    /**
     * @var ValueValidationService
     */
    private $valueValidationService;

    /**
     * @var RuleValidationService
     */
    private $ruleValidationService;

    /**
     * @var RuleRepairService
     */
    private $ruleRepairService;

    /**
     * @var RulePayloadFactory
     */
    private $rulePayloadFactory;

    /**
     * @var JavascriptStateCreatorService
     */
    private $javaScriptStateCreatorService;

    /**
     * @param ConfigurableProductBuilder $configurableProductBuilder
     * @param ProductOrmRepository $productRepository
     * @param RuleValidationService $ruleValidationService
     * @param RulePayloadFactory $rulePayloadFactory
     * @param RenderImageFactory $renderImageFactory
     */
    public function __construct(
        ConfigurableProductBuilder $configurableProductBuilder,
        ProductOrmRepository $productRepository,
        RuleValidationService $ruleValidationService,
        RulePayloadFactory $rulePayloadFactory,
        RenderImageFactory $renderImageFactory
    ) {
        $this->configurableProductFactory = new ConfigurableProductFactory($configurableProductBuilder, $productRepository);
        $this->valueValidationService = new ValueValidationService();
        $this->ruleValidationService = $ruleValidationService;
        $this->ruleRepairService = new RuleRepairService($ruleValidationService, $rulePayloadFactory);
        $this->javaScriptStateCreatorService = new JavascriptStateCreatorService($rulePayloadFactory, $renderImageFactory);
        $this->rulePayloadFactory = $rulePayloadFactory;
    }

    /**
     * @param GetConfigurationState $query
     *
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

        // init state (page is just loaded): Check the validity of incoming data, and set default element values if there are some
        if ($query->getIntention()['init'] ?? false) {
            try {
                // assert valid sections, elements, properties and values (if set)
                $this->valueValidationService->assertValidValues($product, $enrichedState->getState());

                // if this is an initialization (we don't hit the Auswälen button to save/set the state),
                // we just load the page and check that default values are valid
                $this->applyInit($product, $enrichedState);
            } catch (InvalidStateException $e) {
                throw InitConfigurationStateException::fromInvalidConfigurationStateException($e);
            }
        }


        // if validation is passed in above block then
        // apply set/remove/complete actions (add or remove elements from enriched state's disabled array)
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
                sprintf('The state does not fulfill %s rules.', count($failedRules)),
                $failedRules
            );
        }

        // update disabled
        $enrichedState = $this->ruleValidationService->updateDisabled($product, $enrichedState, $validationResult);

        // assert valid sections, elements, properties and values (if set) again after state changes
        $this->valueValidationService->assertValidValues($product, $enrichedState->getState());

        // return js state object
        return $this->javaScriptStateCreatorService->createState($product, $enrichedState, $validationResult, $query->getIntention());
    }

    /**
     * Collects the list of elements and their values, that are marked as 'default' from the backend and gives
     * the list to applySet() method
     *
     * @param ConfigurableProduct $product
     * @param EnrichedState       $state
     *
     * @return void
     * @throws CircularReferenceException
     * @throws InvalidUuidException
     * @throws RepeatableValidationException
     */
    private function applyInit(ConfigurableProduct $product, EnrichedState $state): void
    {
        $calculatedValueName = $this->rulePayloadFactory->getPayload($product, $state->getState(), false);
        $defaultElements = [];

        foreach ($product->getSections() as $section) {
            $sectionId = AptoUuid::fromId($section['id']);

            $sectionCount = 1;
            if ($product->isSectionRepeatable($sectionId)) {
                $sectionCount = $product->getSectionRepetitionCount($sectionId, $calculatedValueName);
            }

            foreach ($section['elements'] as $element) {
                // as the applyInit method should run on initialization (and not on setting new values into the state),
                // we need to check the default values as there are no any other values yet set
                if (!$element['isDefault']) {
                    continue;
                }

                $elementId = AptoUuid::fromId($element['id']);
                $elementDefinition = $product->getElementDefinition($sectionId, $elementId);

                for($repetition = 0; $repetition < $sectionCount; $repetition++) {
                    if ($elementDefinition instanceof ElementDefinitionDefaultValues) {
                        foreach ($elementDefinition->getDefaultValues() as $property => $value) {
                            $defaultElements[] = [
                                'sectionId' => $sectionId->getId(),
                                'elementId' => $elementId->getId(),
                                'sectionRepetition' => $repetition,
                                'property' => $property,
                                'value' => $value
                            ];
                        }
                    } else {
                        $defaultElements[] = [
                            'sectionId' => $sectionId->getId(),
                            'elementId' => $elementId->getId(),
                            'sectionRepetition' => $repetition,
                        ];
                    }
                }
            }
        }

        $this->applySet($product, $state, $defaultElements);
    }

    /**
     * Takes the list of element configs argument, check them and if they are valid updates/sets the state
     *
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param array $items
     *
     * @throws InvalidUuidException
     */
    private function applySet(ConfigurableProduct $product, EnrichedState $state, array $items)
    {
        foreach ($items as $item) {
            $section = AptoUuid::fromId($item['sectionId']);
            $element = AptoUuid::fromId($item['elementId']);

            $property = $item['property'] ?? null;
            $value = $item['value'] ?? null;
            $sectionRepetition = $item['sectionRepetition'] ?? 0;

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
            if ($product->isSectionMultiple($section) === false && $state->getState()->isElementActive($section, $element, $sectionRepetition) === false) {
                $state->getState()->removeSection($section, $sectionRepetition);
            }

            $state->getState()->setValue(
                $section,
                $element,
                $property,
                $value,
                $sectionRepetition
            );
        }
    }

    /**
     * For example triggered when user clicks on "Abwählen" button
     *
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
            $sectionRepetition = $item['sectionRepetition'] ?? 0;

            $this->valueValidationService->assertHasSection($product, $section);
            if ($element) {
                $this->valueValidationService->assertHasElement($product, $section, $element);
            }

            $state->getState()->removeValue(
                $section,
                $element,
                $sectionRepetition
            );
        }
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState       $state
     * @param array               $items
     *
     * @return void
     * @throws InvalidUuidException
     */
    private function applyComplete(ConfigurableProduct $product, EnrichedState $state, array $items)
    {
        foreach ($items as $item) {
            $section = AptoUuid::fromId($item['sectionId']);
            $this->valueValidationService->assertHasSection($product, $section);
            $sectionRepetition = $item['sectionRepetition'] ?? 0;

            $state->setSectionComplete(
                $section,
                boolval($item['complete'] ?? true),
                $sectionRepetition
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
