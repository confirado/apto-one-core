<?php

namespace Apto\Catalog\Domain\Core\Service\EnrichedStateValidation;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\EnrichedState\EnrichedState;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class RuleRepairService
{
    /**
     * @var RuleValidationService
     */
    private $ruleValidationService;

    /**
     * Constructor
     */
    public function __construct(RuleValidationService $ruleValidationService)
    {
        $this->ruleValidationService = $ruleValidationService;
    }

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @param int $maxTries
     * @param array|null $operatorsToFulfill
     * @return RuleValidationResult
     * @throws InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException
     */
    public function repairState(ConfigurableProduct $product, State $state, int $maxTries = 10, ?array $operatorsToFulfill = null): RuleValidationResult
    {
        while(true) {
            // get failed rules
            $validationResult = $this->ruleValidationService->validateState($product, $state);
            if (count($validationResult->getFailed()) === 0 || $maxTries <= 0)
                break;

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
     * @param ConfigurableProduct $product
     * @param EnrichedState $enrichedState
     * @param RuleValidationResult $validationResult
     * @throws InvalidUuidException
     */
    public function selectEmptySections(ConfigurableProduct $product, EnrichedState $enrichedState, RuleValidationResult $validationResult)
    {
        $enrichedState = $this->ruleValidationService->updateDisabled($product, $enrichedState, $validationResult);

        foreach ($product->getSections() as $section) {
            $sectionId = new AptoUuid($section['id']);
            $elementIds = $product->getElementIds($sectionId);

            // skip specific sections
            if (
                $section['isHidden'] ||
                !$section['isMandatory'] ||
                $section['allowMultiple'] ||
                $enrichedState->isSectionDisabled($sectionId, $elementIds) ||
                $enrichedState->getState()->isSectionActive($sectionId)
            ) {
                continue;
            }

            foreach ($section['elements'] as $element) {
                $elementId = new AptoUuid($element['id']);
                $staticValues = $element['definition']['staticValues'];
                $elementDefinitionId = $staticValues['aptoElementDefinitionId'] ?? null;

                if ($enrichedState->isElementDisabled($sectionId, $elementId)) {
                    continue;
                }

                switch ($elementDefinitionId) {

                    // DEFAULT_ELEMENT
                    case 'apto-element-default-element': {
                        $enrichedState->getState()->setValue($sectionId, $elementId);
                        break;
                    }

                    // PRICE_PER_UNIT_ELEMENT
                    case 'apto-element-price-per-unit' : {
                        if (!($staticValues['textBoxEnabled'] ?? false)) {
                            $enrichedState->getState()->setValue(
                                $sectionId,
                                $elementId,
                                'active',
                                true
                            );
                        }
                        break;
                    }

                    // SELECT_BOX_ELEMENT
                    case 'apto-element-select-box': {
                        if ($staticValues['defaultItem'] ?? null) {
                            $enrichedState->getState()->setValue(
                                $sectionId, $elementId, 'aptoElementDefinitionId', 'apto-element-select-box'
                            );
                            $enrichedState->getState()->setValue(
                                $sectionId, $elementId, 'boxes', [$staticValues['defaultItem']]
                            );
                            $enrichedState->getState()->setValue(
                                $sectionId, $elementId, 'selectedItem', $staticValues['defaultItem']['id']
                            );
                            $enrichedState->getState()->setValue(
                                $sectionId, $elementId, 'id', $staticValues['defaultItem']['id']
                            );
                            $enrichedState->getState()->setValue(
                                $sectionId, $elementId, 'name', $staticValues['defaultItem']['name']
                            );
                        }
                        break;
                    }
                }

                // if the current element was selected we break because we only want automatic select one element in every section
                if ($enrichedState->getState()->isElementActive($sectionId, $elementId)) {
                    break;
                }
            }
        }
    }
}
