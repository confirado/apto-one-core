<?php

namespace Apto\Catalog\Domain\Core\Service\EnrichedStateValidation;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\EnrichedState\EnrichedState;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\CompareOperator;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\DefaultCriterion;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayloadFactory;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class RuleValidationService
{
    /**
     * @var RulePayloadFactory
     */
    protected $rulePayloadFactory;

    /**
     * @param RulePayloadFactory $rulePayloadFactory
     */
    public function __construct(RulePayloadFactory $rulePayloadFactory)
    {
        $this->rulePayloadFactory = $rulePayloadFactory;
    }

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @return RuleValidationResult
     * @throws InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException
     */
    public function validateState(ConfigurableProduct $product, State $state): RuleValidationResult
    {
        $affected = [];
        $inactive = [];
        $fulfilled = [];
        $failed = [];
        $ignored = [];

        // ignoredRules is one of the parameters allowed in state together with quantity, see State class
        $ignoredRuleIds = $state->getParameter('ignoredRules');

        // @todo should be done in one operation because of heavy computing time for computed values
        $rulePayload = $this->rulePayloadFactory->getPayload($product, $state);
        $rulePayloadByName = $this->rulePayloadFactory->getPayload($product, $state, false);
        $ruleRepetitionService = new RuleRepetitionService($product, $rulePayloadByName);

        // validate rules and sort them accordingly to their results
        foreach ($ruleRepetitionService->getRules() as $rule) {
            if ($rule->isConditionFulfilled($state, $rulePayload)) {
                $affected[] = $rule;

                if ($rule->isImplicationFulfilled($state, $rulePayload)) {
                    $fulfilled[] = $rule;
                } else {
                    if (in_array($rule->getId()->getId(), $ignoredRuleIds)) {
                        $ignored[] = $rule;
                    } else {
                        $failed[] = $rule;
                    }
                }
            } else {
                $inactive[] = $rule;
            }
        }

        // return new result object
        return new RuleValidationResult(
            $affected,
            $inactive,
            $fulfilled,
            $failed,
            $ignored
        );
    }

    /**
     * @deprecated remove if no restrictions are expected with the new approach for disabled elements
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param RuleValidationResult $validationResult
     * @return EnrichedState
     * @throws InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException
     */
    public function updateDisabledDeprecated(ConfigurableProduct $product, EnrichedState $state, RuleValidationResult $validationResult): EnrichedState
    {
        $updatedState = clone $state;

        foreach ($product->getSections() as $section) {

            $sectionId = new AptoUuid($section['id']);

            foreach ($section['elements'] as $element) {
                $elementId = new AptoUuid($element['id']);

                // clone state
                $testState = clone $state->getState();

                // if element is not active, select it
                // todo consider repetition if used
                if (!$state->getState()->isElementActive($sectionId, $elementId)) {
                    $testState->setValue($sectionId, $elementId);
                }

                // get rule payload for new state
                $testRulePayload = $this->rulePayloadFactory->getPayload($product, $testState);

                // only check previously affected rules
                foreach ($validationResult->getAffected() as $rule) {
                    if (!$rule->isSoft() && !$rule->isImplicationFulfilled($testState, $testRulePayload) && !$validationResult->containsFailed($rule)) {
                        // todo consider repetition if used
                        $updatedState->setElementDisabled(
                            $sectionId,
                            $elementId
                        );
                    }
                }
            }
        }

        return $updatedState;
    }

    /**
     * @param ConfigurableProduct $product
     * @param EnrichedState $state
     * @param RuleValidationResult $validationResult
     * @return EnrichedState
     * @throws InvalidUuidException
     */
    public function updateDisabled(ConfigurableProduct $product, EnrichedState $state, RuleValidationResult $validationResult): EnrichedState
    {
        // we iterate over affected rules (effected means condition met but not implicated) only because in these
        // rules conditions are met, and we want to check here if implications are also ok
        foreach ($validationResult->getAffected() as $rule) {
            if ($rule->isSoft()) {
                continue;
            }

            foreach ($rule->getImplication()->getCriteria() as $criterion) {
                // we check and react only for not active implications as in this we need to disable the element, but when active then it is displayed in any case
                if (!($criterion instanceof DefaultCriterion) || $criterion->getOperator()->getOperator() !== CompareOperator::NOT_ACTIVE) {
                    continue;
                }

                if ($criterion->getElementId()) {
                    $state->setElementDisabled($criterion->getSectionId(), $criterion->getElementId(), true, $criterion->getRepetition());
                } else { // this is the case when rule is applied to section only (see backend)
                    $elementIds = $product->getElementIds($criterion->getSectionId());
                    foreach ($elementIds as $elementId) {
                        $state->setElementDisabled($criterion->getSectionId(), $elementId, true, $criterion->getRepetition());
                    }
                }
            }
        }

        return $state;
    }
}
