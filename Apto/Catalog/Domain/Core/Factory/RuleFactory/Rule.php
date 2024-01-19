<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Condition;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Implication;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\RepeatableValidationException;

class Rule
{
    /**
     * @var AptoUuid
     */
    protected $id;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var bool
     */
    protected $soft;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var AptoTranslatedValue
     */
    protected $errorMessage;

    /**
     * @var Condition
     */
    protected $condition;

    /**
     * @var Implication
     */
    protected $implication;

    /**
     * @var int
     */
    protected $repetition;

    /**
     * @param AptoUuid $id
     * @param bool $active
     * @param bool $soft
     * @param string $name
     * @param AptoTranslatedValue $errorMessage
     * @param Condition $condition
     * @param Implication $implication
     * @param int $repetition
     */
    public function __construct(
        AptoUuid $id,
        bool $active,
        bool $soft,
        string $name,
        AptoTranslatedValue $errorMessage,
        Condition $condition,
        Implication $implication,
        int $repetition = 0
    ) {
        $this->id = $id;
        $this->active = $active;
        $this->soft = $soft;
        $this->name = $name;
        $this->errorMessage = $errorMessage;
        $this->condition = $condition;
        $this->implication = $implication;
        $this->repetition = $repetition;
    }

    /**
     * @return AptoUuid
     */
    public function getId(): AptoUuid
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isSoft(): bool
    {
        return $this->soft;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getErrorMessage(): AptoTranslatedValue
    {
        return $this->errorMessage;
    }

    /**
     * @return Condition
     */
    public function getCondition(): Condition
    {
        return $this->condition;
    }

    /**
     * @return Implication
     */
    public function getImplication(): Implication
    {
        return $this->implication;
    }

    /**
     * @return int
     */
    public function getRepetition(): int
    {
        return $this->repetition;
    }

    /**
     * Check, if condition is fulfilled
     * @param State $state
     * @param RulePayload $rulePayload
     * @return bool
     */
    public function isConditionFulfilled(State $state, RulePayload $rulePayload): bool
    {
        return $this->condition->isFulfilled($state, $rulePayload);
    }

    /**
     * Check, if implication is fulfilled
     * @param State $state
     * @param RulePayload $rulePayload
     * @return bool
     */
    public function isImplicationFulfilled(State $state, RulePayload $rulePayload): bool
    {
        return $this->implication->isFulfilled($state, $rulePayload);
    }

    /**
     * Check, if rule is fulfilled
     * @param State $state
     * @param RulePayload $rulePayload
     * @return bool
     */
    public function isFulfilled(State $state, RulePayload $rulePayload): bool
    {
        return $this->isConditionFulfilled($state, $rulePayload) && $this->isImplicationFulfilled($state, $rulePayload);
    }

    /**
     * Change the given state to fulfill this rule
     * @param ConfigurableProduct $product
     * @param State $state
     * @param array|null $operatorsToFulfill
     * @return State
     */
    public function fulfill(ConfigurableProduct $product, State $state, ?array $operatorsToFulfill = null): State
    {
        return $this->implication->fulfill($product, $state, $operatorsToFulfill);
    }

    /**
     * Return all unique sections ids form a single rule
     *
     * @return AptoUuid[]
     */
    public function getRuleSectionIds(): array
    {
        $sectionIds = [];

        foreach ($this->getCondition()->getCriteria() as $criterion) {
            // If the condition type is not a "Standard" type then skipp because it cannot have applied sections
            if (!($criterion instanceof Rule\DefaultCriterion)) {
                continue;
            }

            $sectionUuId = $criterion->getSectionId();
            $sectionIds[$sectionUuId->getId()] = $sectionUuId;
        }

        foreach ($this->getImplication()->getCriteria() as $criterion) {
            // If the implication type is not a "Standard" type then skipp because it cannot have applied sections
            if (!($criterion instanceof Rule\DefaultCriterion)) {
                continue;
            }

            $sectionUuId = $criterion->getSectionId();
            $sectionIds[$sectionUuId->getId()] = $sectionUuId;
        }

        return $sectionIds;
    }

    /**
     * Return only sections ids form a single rule that are coming from a repeatable section
     *
     * A rule can have many conditions and implications where each on can be made for different sections
     * within the same rule.
     *
     * @return AptoUuid[]
     * @throws RepeatableValidationException
     */
    public function getRuleRepeatableSectionIds(Rule $rule, ConfigurableProduct $product): array
    {
        $sectionUuIds = $this->getRuleSectionIds($rule);

        // this means rule has no section at all
        if (count($sectionUuIds) < 1) {
            return [];
        }

        // now let's see which section ids are coming from repeatable sections
        $repeatableSectionUuIds = [];
        foreach ($sectionUuIds as $sectionUuId) {
            if ($product->isSectionRepeatable($sectionUuId)) {
                $repeatableSectionUuIds[$sectionUuId->getId()] = $sectionUuId;
            }
        }

        return $repeatableSectionUuIds;
    }

    /**
     * Return a human-readable string representation
     * @param ConfigurableProduct $product
     * @param State $state
     * @param RulePayload $rulePayload
     * @return string
     */
    public function explain(ConfigurableProduct $product, State $state, RulePayload $rulePayload): string
    {
        $flags = [];
        if (!$this->active) {
            $flags[] = 'inactive';
        }
        if ($this->soft) {
            $flags[] = 'soft';
        }
        if ($this->isConditionFulfilled($state, $rulePayload)) {
            $flags[] = 'affected';
            $flags[] = $this->isImplicationFulfilled($state, $rulePayload) ? 'fulfilled' : 'failed';
        }

        return sprintf(
            "Rule \"%s\" (%s)\n Uuid: %s\n Repetition (%s) Condition (%s):\n%s\nImplication (%s):\n%s",
            $this->name,
            implode(', ', $flags),
            $this->id->getId(),
            $this->getRepetition(),
            $this->condition->isFulfilled($state, $rulePayload) ? 'fulfilled' : 'failed',
            $this->condition->explain($product, $state, $rulePayload),
            $this->implication->isFulfilled($state, $rulePayload) ? 'fulfilled' : 'failed',
            $this->implication->explain($product, $state, $rulePayload)
        );
    }

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @param RulePayload $rulePayload
     * @return array
     */
    public function toArray(ConfigurableProduct $product, State $state, RulePayload $rulePayload): array
    {
        return [
            'id' => $this->getId(),
            'softRule' => $this->isSoft(),
            'name' => $this->getName(),
            'errorMessage' => $this->getErrorMessage(),
            'explain' => $this->explain($product, $state, $rulePayload)
        ];
    }

}
