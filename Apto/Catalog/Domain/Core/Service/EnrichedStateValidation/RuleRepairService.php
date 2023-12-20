<?php

namespace Apto\Catalog\Domain\Core\Service\EnrichedStateValidation;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\EnrichedState\EnrichedState;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayloadFactory;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\RepeatableValidationException;

class RuleRepairService
{
    /**
     * @var RuleValidationService
     */
    private $ruleValidationService;

    /**
     * @var RulePayloadFactory
     */
    private RulePayloadFactory $rulePayloadFactory;

    /**
     * Constructor
     */
    public function __construct(RuleValidationService $ruleValidationService, RulePayloadFactory $rulePayloadFactory)
    {
        $this->ruleValidationService = $ruleValidationService;
        $this->rulePayloadFactory = $rulePayloadFactory;
    }

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @param int $maxTries
     * @param array|null $operatorsToFulfill
     * @return RuleValidationResult
     * @throws InvalidUuidException
     * @throws CircularReferenceException
     */
    public function repairState(ConfigurableProduct $product, State $state, int $maxTries = 10, ?array $operatorsToFulfill = null): RuleValidationResult
    {
        while(true) {
            // get failed rules
            $validationResult = $this->ruleValidationService->validateState($product, $state);

            // if even one rule is not valid, or we have exited $maxTries count then stop and return
            if (count($validationResult->getFailed()) === 0 || $maxTries <= 0) {
                break;
            }

            // fulfill first failed rule
            $state = $validationResult->getFailed()[0]->fulfill($product, $state, $operatorsToFulfill);

            $maxTries--;
        }

        return $validationResult;
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $enrichedState
     * @param RuleValidationResult $validationResult
     * @param int $maxTries
     * @param array|null $operatorsToFulfill
     * @return RuleValidationResult
     * @throws InvalidUuidException
     */
    public function repairStateAndSelectEmptySections(ConfigurableProduct $product, EnrichedState $enrichedState, RuleValidationResult $validationResult, int $maxTries = 10, ?array $operatorsToFulfill = null): RuleValidationResult
    {
        $this->selectEmptySections($product, $enrichedState, $validationResult);
        $validationResult = $this->ruleValidationService->validateState($product, $enrichedState->getState());

        $tries = $maxTries;
        while (count($validationResult->getFailed()) > 0 && $tries > 0) {
            $validationResult = $this->repairState($product, $enrichedState->getState(), $maxTries, $operatorsToFulfill);
            $this->selectEmptySections($product, $enrichedState, $validationResult);
            $tries--;
        }

        return $validationResult;
    }

    /**
     * @param ConfigurableProduct  $product
     * @param EnrichedState        $enrichedState
     * @param RuleValidationResult $validationResult
     *
     * @return void
     * @throws InvalidUuidException
     * @throws CircularReferenceException
     * @throws RepeatableValidationException
     */
    public function selectEmptySections(ConfigurableProduct $product, EnrichedState $enrichedState, RuleValidationResult $validationResult): void
    {
        $enrichedState = $this->ruleValidationService->updateDisabled($product, $enrichedState, $validationResult);
        $rulePayload = $this->rulePayloadFactory->getPayload($product, $enrichedState->getState(), false);

        foreach ($product->getSections() as $section) {
            $sectionId = new AptoUuid($section['id']);
            $elementIds = $product->getElementIds($sectionId);

            $sectionCount = 1;
            if ($product->isSectionRepeatable($sectionId)) {
                $sectionCount = $product->getSectionRepetitionCount($sectionId, $rulePayload);
            }

            for($repetition = 0; $repetition < $sectionCount; $repetition++) {
                // skip specific sections
                if (
                    $section['isHidden'] ||
                    !$section['isMandatory'] ||
                    $section['allowMultiple'] ||
                    $enrichedState->isSectionDisabled($sectionId, $elementIds, $repetition) ||
                    $enrichedState->getState()->isSectionActive($sectionId, $repetition)
                ) {
                    continue;
                }

                foreach ($section['elements'] as $element) {
                    $elementId = new AptoUuid($element['id']);
                    $staticValues = $element['definition']['staticValues'];
                    $elementDefinitionId = $staticValues['aptoElementDefinitionId'] ?? null;

                    if ($enrichedState->isElementDisabled($sectionId, $elementId, $repetition)) {
                        continue;
                    }

                    switch ($elementDefinitionId) {

                        // DEFAULT_ELEMENT
                        case 'apto-element-default-element': {
                            $enrichedState->getState()->setValue($sectionId, $elementId, null, null, $repetition);
                            break;
                        }

                        // PRICE_PER_UNIT_ELEMENT
                        case 'apto-element-price-per-unit' : {
                            if (!($staticValues['textBoxEnabled'] ?? false)) {
                                $enrichedState->getState()->setValue(
                                    $sectionId,
                                    $elementId,
                                    'active',
                                    true,
                                    $repetition,
                                );
                            }
                            break;
                        }

                        // SELECT_BOX_ELEMENT
                        case 'apto-element-select-box': {
                            if ($staticValues['defaultItem'] ?? null) {
                                $enrichedState->getState()->setValue(
                                    $sectionId, $elementId, 'aptoElementDefinitionId', 'apto-element-select-box', $repetition
                                );
                                $enrichedState->getState()->setValue(
                                    $sectionId, $elementId, 'boxes', [$staticValues['defaultItem']], $repetition
                                );
                                $enrichedState->getState()->setValue(
                                    $sectionId, $elementId, 'selectedItem', $staticValues['defaultItem']['id'], $repetition
                                );
                                $enrichedState->getState()->setValue(
                                    $sectionId, $elementId, 'id', $staticValues['defaultItem']['id'], $repetition
                                );
                                $enrichedState->getState()->setValue(
                                    $sectionId, $elementId, 'name', $staticValues['defaultItem']['name'], $repetition
                                );
                            }
                            break;
                        }
                    }

                    // if the current element was selected we break because we only want automatic select one element in every section
                    if ($enrichedState->getState()->isElementActive($sectionId, $elementId, $repetition)) {
                        break;
                    }
                }
            }
        }
    }
}
