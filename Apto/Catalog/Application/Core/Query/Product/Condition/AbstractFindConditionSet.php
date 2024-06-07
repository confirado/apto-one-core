<?php

namespace Apto\Catalog\Application\Core\Query\Product\Condition;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindConditionSet implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $conditionSetId;

    /**
     * @param string $conditionSetId
     */
    public function __construct(string $conditionSetId)
    {
        $this->conditionSetId = $conditionSetId;
    }

    /**
     * @return string
     */
    public function getConditionSetId(): string
    {
        return $this->conditionSetId;
    }
}
