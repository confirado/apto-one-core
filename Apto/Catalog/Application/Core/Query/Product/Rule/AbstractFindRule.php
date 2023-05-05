<?php

namespace Apto\Catalog\Application\Core\Query\Product\Rule;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindRule implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $ruleId;

    /**
     * FindRule constructor.
     * @param string $ruleId
     */
    public function __construct(string $ruleId)
    {
        $this->ruleId = $ruleId;
    }

    /**
     * @return string
     */
    public function getRuleId(): string
    {
        return $this->ruleId;
    }
}