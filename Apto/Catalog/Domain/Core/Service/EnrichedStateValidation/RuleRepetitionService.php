<?php

namespace Apto\Catalog\Domain\Core\Service\EnrichedStateValidation;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\RuleFactory;
use Apto\Catalog\Domain\Core\Model\Product\RepeatableValidationException;

/**
 * On creating repeatable sections, if there are some rules that use the repeatable section, we want to duplicate
 * those rules together with automatically created sections. For each added section, we create a new rule.
 * In this way, we can have different rules for each repeated section and not one for all of them.
 *
 * But do with some restrictions!
 *
 * Rules that are meant to be used for repeatable sections (Sektionstype: Wiederholbar) have some
 * restrictions (see below). Therefore, before applying the rule for a repeatable section (or before duplicating it
 * for each repeated section), we need to follow these restrictions:
 *
 * 1. Conditions and implications must be of type "Standard"
 *    A rule in Apto can have two types of conditions and implications: "Standard" (DefaultCriterion) and
 *    "Berechneter Wert" (ComputedProductValueCriterion). And only in case of "Standard" we need to provide
 *    sections. Therefore, we need to check the "Standard" type before duplicating the rules as we don't
 *    need to duplicate the rule if it is not applied to a section.
 *
 * 2. If the section is repeatable or not
 *    If the section is repeatable then and only then we duplicate the rules (obviously)
 *
 * 3. If conditions and implications of the rule are selected to be applied for only one section.
 *    If the section is repeatable, then all conditions (Bedingung) and implications (Auswirkung) for that rule
 *    must be written only for the same section. In other words, they must have the same "sectionId" if the
 *    section is repeatable.
 *    We cannot have in the rule, conditions or implications that point to different REPEATABLE sections,
 *    because otherwise we get hard manageable code.
 *    But within the same rule, we can have different section ids if they are coming from not repeatable sections
 *
 * This class needs to be updated everytime a new property is added or changed in the rules !!!
 */
class RuleRepetitionService
{
    /**
     * @var Rule[]
     */
    private array $rules;

    /**
     * @var ConfigurableProduct
     */
    private ConfigurableProduct $product;

    /**
     * @var RulePayload
     */
    private RulePayload $rulePayloadByName;

    /**
     * @param ConfigurableProduct $product
     * @param RulePayload         $rulePayloadByName
     *
     * @throws InvalidUuidException
     * @throws RepeatableValidationException
     */
    public function __construct(ConfigurableProduct $product, RulePayload $rulePayloadByName)
    {
        $this->product = $product;
        $this->rulePayloadByName = $rulePayloadByName;
        $this->rules = $this->getRepetitionRules();
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Return the list of the duplicated rules together with other rules
     *
     * @return array|Rule[]
     * @throws InvalidUuidException
     * @throws RepeatableValidationException
     */
    private function getRepetitionRules(): array
    {
        $this->rules = [];

        foreach ($this->product->getRules() as $rule) {
            $sectionUuId = $this->getRepeatableSectionIdFromRule($rule);

            /*  If everything is ok, then duplicate the rule for each repetition and add all duplicated rules to the
                existing rules, otherwise add the rule only once (as in normal case like in non-repeatable section)  */
            if (!is_null($sectionUuId)) {
                $this->rules = array_merge($this->rules, $this->createRepetitionRules($rule, $sectionUuId));
            } else {
                $this->rules[] = $rule;
            }
        }

        return $this->rules;
    }

    /**
     * Takes a single rule and then duplicates it if the rule is in a repeatable section
     *
     * @param Rule     $rule
     * @param AptoUuid $sectionId
     *
     * @return array
     * @throws InvalidUuidException
     * @throws RepeatableValidationException
     */
    private function createRepetitionRules(Rule $rule, AptoUuid $sectionId): array
    {
        $rules = [];
        $sectionRepetitionCount = $this->product->getSectionRepetitionCount($sectionId, $this->rulePayloadByName);

        for($repetition = 0; $repetition < $sectionRepetitionCount; $repetition++) {
            $rawRule = [
                'id' => $rule->getId()->getId(),
                'active' => $rule->isActive(),
                'name' => $rule->getName(),
                'errorMessage' => $rule->getErrorMessage()->jsonSerialize(),
                'softRule' => $rule->isSoft(),
                'repetition' => $repetition,
                'conditionsOperator' => $rule->getCondition()->getOperator()->getOperator(),
                'implicationsOperator' => $rule->getImplication()->getOperator()->getOperator(),
                'conditions' => [],
                'implications' => [],
            ];

            foreach ($rule->getCondition()->getCriteria() as $criterion) {

                // If the condition type is not a "Standard" type then skipp it, because it cannot have sections
                if (!($criterion instanceof Rule\DefaultCriterion)) {
                    continue;
                }

                $rawRule['conditions'][] = [
                    'type' => $criterion::TYPE,
                    'sectionId' => $criterion->getSectionId()->getId(),
                    'elementId' => $criterion->getElementId()?->getId(),
                    'property' => $criterion->getProperty(),
                    'operator' => $criterion->getOperator()->getOperator(),
                    'value' => $criterion->getValue(),
                    'repetition' => $repetition,
                ];
            }

            foreach ($rule->getImplication()->getCriteria() as $criterion) {

                // If the condition type is not a "Standard" type then skipp it, because it cannot have applied sections
                if (!($criterion instanceof Rule\DefaultCriterion)) {
                    continue;
                }

                $rawRule['implications'][] = [
                    'type' => $criterion::TYPE,
                    'sectionId' => $criterion->getSectionId()->getId(),
                    'elementId' => $criterion->getElementId()?->getId(),
                    'property' => $criterion->getProperty(),
                    'operator' => $criterion->getOperator()->getOperator(),
                    'value' => $criterion->getValue(),
                    'repetition' => $repetition,
                ];
            }

            $rules[] = RuleFactory::fromArray($rawRule);
        }

        return $rules;
    }

    /**
     * Returns the only possible section id for the given rule
     *
     * As described in class description, a repeatable section can have only one section id, and here we try to get it
     *
     * @param Rule $rule
     *
     * @return AptoUuid|null
     * @throws RepeatableValidationException
     */
    private function getRepeatableSectionIdFromRule(Rule $rule): ?AptoUuid
    {
        $sectionUuIds = $rule->getRuleSectionIds();

        // this means the rule has no section at all
        if (count($sectionUuIds) === 0) {
            return null;
        }

        // now let's see which section ids are coming from repeatable sections
        $repeatableSectionUuIds = $rule->getRuleRepeatableSectionIds($rule, $this->product);

        // We expect to have one, and ONLY one, section in $repeatableSections for both the conditions and implications
        if (count($repeatableSectionUuIds) !== 1) {
            return null;
        }

        return array_values($repeatableSectionUuIds)[0];
    }
}
