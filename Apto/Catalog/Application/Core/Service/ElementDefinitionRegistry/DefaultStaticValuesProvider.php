<?php

namespace Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Catalog\Domain\Core\Model\Product\Element\DefaultElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

class DefaultStaticValuesProvider implements ElementStaticValuesProvider
{
    /**
     * @return string
     */
    public function getElementDefinitionClass(): string
    {
        return DefaultElementDefinition::class;
    }

    /**
     * @param ElementDefinition $elementDefinition
     * @return array
     */
    public function getStaticValues(ElementDefinition $elementDefinition): array
    {
        return $elementDefinition->getStaticValues();
    }
}
