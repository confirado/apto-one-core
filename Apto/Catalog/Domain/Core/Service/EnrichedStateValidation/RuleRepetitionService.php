<?php

namespace Apto\Catalog\Domain\Core\Service\EnrichedStateValidation;

use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;

class RuleRepetitionService
{
    /**
     * @var array
     */
    private array $rules;

    /**
     * @var ConfigurableProduct
     */
    private ConfigurableProduct $product;

    /**
     * @var RulePayload
     */
    private RulePayload $rulePayload;

    /**
     * @param ConfigurableProduct $product
     * @param RulePayload $rulePayload
     */
    public function __construct(ConfigurableProduct $product, RulePayload $rulePayload)
    {
        $this->product = $product;
        $this->rulePayload = $rulePayload;
        $this->rules = $this->getRepetitionRules();
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    private function getRepetitionRules(): array
    {
        return $this->areAllRepetitionSectionsEqual() ? $this->createRepetitionRules() : $this->product->getRules();
    }

    /**
     * @return bool
     */
    private function areAllRepetitionSectionsEqual(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    private function createRepetitionRules(): array
    {
        return $this->product->getRules();
    }
}
