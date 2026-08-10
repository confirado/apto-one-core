<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

interface EffectiveElementValue
{
    /**
     * @param array $computedValues
     * @return ElementValue
     */
    public function withEffectiveValues(array $computedValues): ElementValue;

    /**
     * @return bool
     */
    public function isEffective(): bool;
}
