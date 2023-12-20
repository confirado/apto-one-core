<?php

namespace Apto\Catalog\Domain\Core\Service\EnrichedStateValidation;

use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;

class RuleValidationResult
{
    /**
     * Condition met
     * Implication not done
     *
     * @var Rule[]
     */
    protected $affected;

    /**
     * Condition not met
     * Implication not done
     *
     * @var Rule[]
     */
    protected $inactive;

    /**
     * Condition met
     * Implication done
     *
     * @var Rule[]
     */
    protected $fulfilled;

    /**
     * Condition not met
     * Implication not done
     * Rule was not ignored as well
     *
     * @var Rule[]
     */
    protected $failed;

    /**
     * Condition not met
     * Implication not done
     * Rule was among ignored rules
     *
     *  @var Rule[]
     */
    protected $ignored;

    /**
     * @param Rule[] $affected
     * @param Rule[] $inactive
     * @param Rule[] $fulfilled
     * @param Rule[] $failed
     * @param Rule[] $ignored
     */
    public function __construct(array $affected, array $inactive, array $fulfilled, array $failed, array $ignored)
    {
        $this->affected = $affected;
        $this->inactive = $inactive;
        $this->fulfilled = $fulfilled;
        $this->failed = $failed;
        $this->ignored = $ignored;
    }

    /**
     * @return Rule[]
     */
    public function getAffected(): array
    {
        return $this->affected;
    }

    /**
     * @return Rule[]
     */
    public function getInactive(): array
    {
        return $this->inactive;
    }

    /**
     * Return all rules with fulfilled condition and implication
     * @return Rule[]
     */
    public function getFulfilled(): array
    {
        return $this->fulfilled;
    }

    /**
     * Return all rules with fulfilled condition and failed implication, that haven't been ignored
     * @return Rule[]
     */
    public function getFailed(): array
    {
        return $this->failed;
    }

    /**
     * Return all rules with fulfilled condition and failed implication, that have been ignored
     * @return Rule[]
     */
    public function getIgnored(): array
    {
        return $this->ignored;
    }

    /**
     * @param Rule $rule
     * @return bool
     */
    public function containsFailed(Rule $rule): bool
    {
        foreach ($this->failed as $failed) {
            if ($rule->getId()->getId() === $failed->getId()->getId()) {
                return true;
            }
        }
        return false;
    }

}
