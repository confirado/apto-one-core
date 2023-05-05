<?php

namespace Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

interface ElementStaticValuesProvider
{
    /**
     * @return string
     */
    public function getElementDefinitionClass(): string;

    /**
     * @param ElementDefinition $elementDefinition
     * @return array
     */
    public function getStaticValues(ElementDefinition $elementDefinition): array;
}
