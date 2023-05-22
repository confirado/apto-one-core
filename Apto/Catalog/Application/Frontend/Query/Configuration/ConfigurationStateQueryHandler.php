<?php

namespace Apto\Catalog\Application\Frontend\Query\Configuration;

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
     * @param rulePayloadFactory $rulePayloadFactory
     */
    public function __construct(
        ConfigurableProductBuilder $configurableProductBuilder,
        ProductOrmRepository $productRepository,
        RuleValidationService $ruleValidationService,
        RulePayloadFactory $rulePayloadFactory
    ) {
        $this->configurableProductFactory = new ConfigurableProductFactory($configurableProductBuilder, $productRepository);
        $this->valueValidationService = new ValueValidationService();
        $this->ruleValidationService = $ruleValidationService;
        $this->ruleRepairService = new RuleRepairService($ruleValidationService);
        $this->javaScriptStateCreatorService = new JavascriptStateCreatorService($rulePayloadFactory);
        $this->rulePayloadFactory = $rulePayloadFactory;
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

        // return js state object
        return $this->javaScriptStateCreatorService->createState($product, $enrichedState, $validationResult, $query->getIntention());
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
