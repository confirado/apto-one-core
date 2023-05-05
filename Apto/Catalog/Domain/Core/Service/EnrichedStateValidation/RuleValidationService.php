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

        $ignoredRuleIds = $state->getParameter('ignoredRules');

        $rulePayload = $this->rulePayloadFactory->getPayload($product, $state);

        // validate rules and sort them accordingly to their results
        foreach ($product->getRules() as $rule) {
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
                if (!$state->getState()->isElementActive($sectionId, $elementId)) {
                    $testState->setValue($sectionId, $elementId);
                }

                // get rule payload for new state
                $testRulePayload = $this->rulePayloadFactory->getPayload($product, $testState);

                // only check previously affected rules
                foreach ($validationResult->getAffected() as $rule) {
                    if (!$rule->isSoft() && !$rule->isImplicationFulfilled($testState, $testRulePayload) && !$validationResult->containsFailed($rule)) {
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
        foreach ($validationResult->getAffected() as $rule) {
            if ($rule->isSoft()) {
                continue;
            }

            foreach ($rule->getImplication()->getCriteria() as $criterion) {
                if (!($criterion instanceof DefaultCriterion) || $criterion->getOperator()->getOperator() !== CompareOperator::NOT_ACTIVE) {
                    continue;
                }

                if ($criterion->getElementId()) {
                    $state->setElementDisabled($criterion->getSectionId(), $criterion->getElementId());
                } else {
                    $elementIds = $product->getElementIds($criterion->getSectionId());
                    foreach ($elementIds as $elementId) {
                        $state->setElementDisabled($criterion->getSectionId(), $elementId);
                    }
                }
            }
        }

        return $state;
    }
}
