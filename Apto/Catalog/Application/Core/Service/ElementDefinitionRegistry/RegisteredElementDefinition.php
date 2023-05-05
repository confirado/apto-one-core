<?php

namespace Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

interface RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string;

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string;

    /**
     * @return string
     */
    public function getBackendComponent(): string;

    /**
     * @return string
     */
    public function getFrontendComponent(): string;

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition;
}