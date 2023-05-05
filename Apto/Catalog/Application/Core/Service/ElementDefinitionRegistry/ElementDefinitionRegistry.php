<?php

namespace Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

class ElementDefinitionRegistry implements \JsonSerializable
{
    /**
     * @var array
     */
    private $registeredElementDefinitions = [];

    /**
     * @var array
     */
    private $staticValuesProviders = [];

    /**
     * @param RegisteredElementDefinition $elementDefinitionToRegister
     */
    public function registerElementDefinition(RegisteredElementDefinition $elementDefinitionToRegister)
    {
        $className = $elementDefinitionToRegister->getElementDefinitionClassName();
        if (isset($this->registeredElementDefinitions[$className])) {
            throw new \InvalidArgumentException('ClassName \' ' . $className . ' \' is already registered. You cannot register a className twice');
        }

        if (!class_exists($className)) {
            throw new \InvalidArgumentException('ClassName \' ' . $className . ' \' was not found.');
        }

        $this->registeredElementDefinitions[$className] = $elementDefinitionToRegister;
    }

    /**
     * @return array
     */
    public function getRegisteredElementDefinitions(): array
    {
        return $this->registeredElementDefinitions;
    }

    /**
     * @param $className
     * @return RegisteredElementDefinition
     */
    public function getRegisteredElementDefinition($className): RegisteredElementDefinition
    {
        if (!isset($this->registeredElementDefinitions[$className])) {
            throw new \InvalidArgumentException('ElementDefinition \' ' . $className . ' \' was not registered.');
        }

        return $this->registeredElementDefinitions[$className];
    }

    /**
     * @param ElementStaticValuesProvider $elementStaticValuesProvider
     * @return void
     */
    public function addStaticValuesProvider(ElementStaticValuesProvider $elementStaticValuesProvider)
    {
        $elementDefinition = $elementStaticValuesProvider->getElementDefinitionClass();
        if (array_key_exists($elementDefinition, $this->staticValuesProviders)) {
            throw new \InvalidArgumentException('A element static values provider with an id \'' . $elementDefinition . '\' is already registered.');
        }

        $this->staticValuesProviders[$elementDefinition] = $elementStaticValuesProvider;
    }

    /**
     * @param string $elementDefinitionClass
     * @return ElementStaticValuesProvider|null
     */
    public function getStaticValuesProvider(string $elementDefinitionClass): ElementStaticValuesProvider
    {
        if (!array_key_exists($elementDefinitionClass, $this->staticValuesProviders)) {
            return new DefaultStaticValuesProvider();
        }

        return $this->staticValuesProviders[$elementDefinitionClass];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $definitions = [];

        foreach ($this->registeredElementDefinitions as $registeredElementDefinition) {
            /** @var RegisteredElementDefinition $registeredElementDefinition */
            $definitions[] = [
                'name' => $registeredElementDefinition->getElementDefinitionName(),
                'className' => $registeredElementDefinition->getElementDefinitionClassName(),
                'backendComponent' => $registeredElementDefinition->getBackendComponent(),
                'frontendComponent' => $registeredElementDefinition->getFrontendComponent()
            ];
        }

        return $definitions;
    }
}
