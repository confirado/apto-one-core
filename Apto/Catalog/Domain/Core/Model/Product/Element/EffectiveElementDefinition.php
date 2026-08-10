<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

interface EffectiveElementDefinition
{
    /**
     * @param array $computedValues
     * @return ElementDefinition
     */
    public function withEffectiveValues(array $computedValues): ElementDefinition;
}
