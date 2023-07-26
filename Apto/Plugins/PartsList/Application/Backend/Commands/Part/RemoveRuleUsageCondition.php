<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

class RemoveRuleUsageCondition extends RemoveUsage
{
    /**
     * @var string
     */
    private $conditionId;

    /**
     * RemoveRuleUsageCondition constructor.
     * @param string $partId
     * @param string $usageId
     * @param string $conditionId
     */
    public function __construct(string $partId, string $usageId, string $conditionId)
    {
        parent::__construct($partId, $usageId);
        $this->conditionId = $conditionId;
    }

    /**
     * @return string
     */
    public function getConditionId(): string
    {
        return $this->conditionId;
    }


}