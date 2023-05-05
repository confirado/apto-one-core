<?php

namespace Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Catalog\Domain\Core\Model\Product\Element\DefaultElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

class RegisteredDefaultElementDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return DefaultElementDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return DefaultElementDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return DefaultElementDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return DefaultElementDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        return new DefaultElementDefinition();
    }
}