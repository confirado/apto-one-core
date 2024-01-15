<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration\State;

use Apto\Base\Domain\Core\Model\AptoJsonSerializable;
use Apto\Base\Domain\Core\Model\AptoUuid;

class State implements AptoJsonSerializable, \JsonSerializable
{
    /**
     * parameters
     */
    const QUANTITY = 'quantity';
    const IGNORED_RULES = 'ignoredRules';
    const REPETITIONS = 'repetitions';

    /**
     * list of all possible parameters with their default values
     */
    const PARAMETERS = [
        self::QUANTITY => 1,
        self::IGNORED_RULES => [],
        self::REPETITIONS => 1,
    ];

    /**
     * Configuration state for element configs
     *
     * @var array
     */
    protected array $state;


    /**
     * Holds the parameters that are saved in state and have some value but not directly related to element configs
     *
     * @var array
     */
    protected array $parameters;


    /**
     * @param array $state An array with keys 'state' and 'properties', each containing an array.
     */
    public function __construct(array $state = [])
    {
        $this->state = [];

        if(isset($state['state'])) {
            // remove empty branches from state...
            $keysToRemove = [];
            foreach ($state['state'] as $key => $st) {
                if (empty($st['values'])) {
                    $keysToRemove[] = $key;
                }
            }
            $this->unsetStateItems($keysToRemove);

            // ...and then add new values
            $this->state = $state['state'];
        }

        $this->parameters = $state['parameters'] ?? [];
    }

    /**
     * Parameters are special cases that exit in state, but are not directly linked to product, product section
     * and/or product elements. Therefor we need to handle them separately
     *
     * for example quantity of selected items is parameter
     *
     * @param string $stateItem
     *
     * @return bool
     */
    public function isParameter(string $stateItem): bool
    {
        return array_key_exists($stateItem, self::PARAMETERS);
    }

    /**
     * Checks that the given configuration item is a parameter config
     *
     * parameters have at least one array key that is a parameter
     *
     * @param array $items
     *
     * @return bool
     */
    public function isParameterConfig(array $items): bool
    {
        foreach ($items as $property) {
            if ($this->isParameter($property)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $parameterName
     *
     * @return bool
     */
    public function isParameterSet(string $parameterName): bool
    {
        return array_key_exists($parameterName, $this->parameters);
    }

    /**
     * Get parameter value
     *
     * if parameter value is set returns it's value, otherwise returns the default value
     *
     * @param string $name
     * @param bool   $returnDefault
     *
     * @return mixed
     */
    public function getParameter(string $name, bool $returnDefault = true): mixed
    {
        if (!$this->isParameter($name)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid parameter.', $name));
        }

        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }

        return $returnDefault ? self::PARAMETERS[$name] : null;
    }

    /**
     * Sets the parameter value
     *
     * Caution: you can not set parameters dynamically, first you need to add it to 'PARAMETERS' constant,
     * then create a separate constant for it and only then you can set it
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function setParameter(string $name, mixed $value): void
    {
        if (!$this->isParameter($name)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid parameter.', $name));
        }

        $this->parameters[$name] = $value;
    }

    public function removeParameter(string $name): void
    {
        if (!$this->isParameter($name)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid parameter.', $name));
        }

        unset($this->parameters[$name]);
    }

    /**
     * Returns all parameters that have set value (exist in state)
     *
     * @return array
     */
    public function getParameterList(): array
    {
        return $this->parameters;
    }

    /**
     * Apply all missing parameters with their default values to given state
     *
     * @return array
     */
    protected function applyMissingParameters(): array
    {
        return [
            'state'      => $this->state,
            'parameters' => $this->parameters
        ];
    }

    /**
     * Section is active when we have at one selected element from that section, in that case a record is added into
     * the state array. That's why we check to find that record
     *
     * @param AptoUuid $sectionId
     * @param int      $repetition
     *
     * @return bool
     */
    public function isSectionActive(AptoUuid $sectionId, int $repetition = 0): bool
    {
        foreach ($this->state as $state) {
            if ($state['sectionId'] === $sectionId->getId() && $state['repetition'] === $repetition) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param int      $repetition
     *
     * @return bool
     */
    public function isElementActive(AptoUuid $sectionId, AptoUuid $elementId, int $repetition = 0): bool
    {
        foreach ($this->state as $state) {
            if ($state['sectionId'] === $sectionId->getId() &&
                $state['elementId'] === $elementId->getId() &&
                $state['repetition'] === $repetition
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string   $property
     * @param int      $repetition
     *
     * @return bool
     */
    public function isPropertyActive(AptoUuid $sectionId, AptoUuid $elementId, string $property, int $repetition = 0): bool
    {
        foreach ($this->state as $state) {
            if ($state['sectionId'] === $sectionId->getId() &&
                $state['elementId'] === $elementId->getId() &&
                $state['repetition'] === $repetition &&
                !empty($state['values']) &&
                array_key_exists($property, $state['values'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param int      $repetition
     *
     * @return array|null
     */
    public function getValues(AptoUuid $sectionId, AptoUuid $elementId, int $repetition = 0): ?array
    {
        foreach ($this->state as $state) {
            if ($state['sectionId'] === $sectionId->getId() &&
                $state['elementId'] === $elementId->getId() &&
                $state['repetition'] === $repetition &&
                !empty($state['values'])) {
                return $state['values'];
            }
        }
        return null;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string   $property
     * @param int      $repetition
     *
     * @return mixed|null
     */
    public function getValue(AptoUuid $sectionId, AptoUuid $elementId, string $property, int $repetition = 0)
    {
        foreach ($this->state as $state) {
            if ($state['sectionId'] === $sectionId->getId() &&
                $state['elementId'] === $elementId->getId() &&
                $state['repetition'] === $repetition &&
                !empty($state['values']) &&
                array_key_exists($property, $state['values'])
            ) {
                return $state['values'][$property];
            }
        }

        return null;
    }

    /**
     * Returns sections ids as key and true as value
     *
     * @return array
     */
    public function getSectionIds(): array
    {
        $sectionList = [];
        foreach ($this->state as $state) {
            if (!$this->isParameter($state['sectionId'])) {
                $sectionList[$state['sectionId']] = true;
            }
        }
        return $sectionList;
    }

    /**
     * Returns sections with its repetition
     * @return array
     */
    public function getSectionList(): array
    {
        $sectionList = [];
        $lookup = [];

        foreach ($this->state as $state) {
            if (!$this->isParameter($state['sectionId']) && !array_key_exists($state['sectionId'] . $state['repetition'], $lookup)) {
                $sectionList[] = [
                    'sectionId' => $state['sectionId'],
                    'repetition' => $state['repetition']
                ];
                $lookup[$state['sectionId'] . $state['repetition']] = true;
            }
        }
        return $sectionList;
    }

    /**
     * Old return:
     *
     * Array(
     *  [ee81ab8f-24 33-490a-99a1-80d7399db85d] => 1
     *  [7d4cbc8d-3f40-42b6-99cc-1c42b9aedba4] => 1
     *  [5a41d5c5-b35b-4aa0-8b9b-ac6ae94bcd49] => Array
     *  (
     *     [height] => 999
     *     [width] => 999
     *  )
     * )
     *
     * @return array
     */
    public function getElementList(): array
    {
        $elementList = [];
        foreach ($this->state as $state) {
            if (!$this->isParameter($state['sectionId'])) {
                // we can have multiple elements with the same element id if a section type is "wiederholbar", therefor
                // we take the whole state item into element list
                $elementList[] = $state;
            }
        }
        return $elementList;
    }

    /**
     * This should be used for setting state for configuration elements and not for parameter
     * for parameters we have different methods
     *
     * @param AptoUuid    $sectionId
     * @param AptoUuid    $elementId
     * @param string|null $property is null on default element, or when element has no properties at all (has no selectable values in element definition)
     * @param mixed|null  $value
     * @param int         $repetition
     *
     * @return State
     */
    public function setValue(AptoUuid $sectionId, AptoUuid $elementId, string $property = null, mixed $value = null, int $repetition = 0): State
    {
        if ($this->isParameter($sectionId)) {
            throw new \InvalidArgumentException(sprintf('Parameters must be set from setParameter() method'));
        }

        // if an element isn't found in the state create a new entry for it
        if (!$this->isElementActive($sectionId, $elementId, $repetition)) {
            $this->state[] = [
                'repetition' => $repetition,
                'sectionId' => $sectionId->getId(),
                'elementId' => $elementId->getId(),
                'values' => $property !== null ? [$property => $value] : []
            ];
        }
        else {
            // if element is found update hte value
            foreach ($this->state as $key => &$state) {
                if ($state['sectionId'] === $sectionId->getId() &&
                    $state['elementId'] === $elementId->getId() &&
                    $state['repetition'] === $repetition
                ) {
                    if ($property !== null) {
                        $state['values'][$property] = $value;
                    }
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * @param AptoUuid      $sectionId
     * @param AptoUuid|null $elementId
     * @param int           $repetition
     *
     * @return $this
     */
    public function removeValue(AptoUuid $sectionId, AptoUuid $elementId = null, int $repetition = 0): State
    {
        if ($elementId) {
            $keysToRemove = [];
            foreach ($this->state as $key => $state) {
                if ($state['sectionId'] === $sectionId->getId() &&
                    $state['elementId'] === $elementId->getId() &&
                    $state['repetition'] === $repetition
                ) {
                    $keysToRemove[] = $key;
                }
            }
            $this->unsetStateItems($keysToRemove);
        }
        else {
            $keysToRemove = [];
            foreach ($this->state as $key => $state) {
                if ($state['sectionId'] === $sectionId->getId() && $state['repetition'] === $repetition) {
                    $keysToRemove[] = $key;
                }
            }
            $this->unsetStateItems($keysToRemove);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param int      $repetition
     *
     * @return $this
     */
    public function removeSection(AptoUuid $sectionId, int $repetition = 0): State
    {
        return $this->removeValue($sectionId, null, $repetition);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param int      $repetition
     *
     * @return $this
     */
    public function removeElement(AptoUuid $sectionId, AptoUuid $elementId, int $repetition = 0): State
    {
        return $this->removeValue($sectionId, $elementId, $repetition);
    }

    /**
     * @param AptoUuid $sectionId
     * @param int      $repetition
     *
     * @return $this
     */
    public function removeValues(AptoUuid $sectionId, int $repetition = 0): State
    {
        return $this->removeValue($sectionId, null, $repetition);
    }

    /**
     * Return raw state with all parameters
     * @return array
     */
    public function getState(): array
    {
        return $this->applyMissingParameters();
    }

    /**
     * Return raw state without parameters
     *
     * @return array
     */
    public function getStateWithoutParameters(): array
    {
        return $this->state;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param int      $repetition
     *
     * @return array|null
     */
    public function getElementState(AptoUuid $sectionId, AptoUuid $elementId, int $repetition = 0): ?array
    {
        if (!$this->isElementActive($sectionId, $elementId, $repetition)) {
            return null;
        }

        foreach ($this->state as $state) {
            if ($state['sectionId'] === $sectionId->getId() &&
                $state['elementId'] === $elementId->getId() &&
                $state['repetition'] === $repetition
            ) {
                return $state['values'];
            }
        }
        return null;
    }

    /**
     * @param State $state
     * @return bool
     */
    public function equals(State $state): bool
    {
        return $this->state == $state->getState();
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
            'json' => [
                'state' => $this->state
            ]
        ];
    }

    /**
     * @param array $json
     * @return State
     */
    public static function jsonDecode(array $json): State
    {
        if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'State\' due to wrong class namespace.');
        }
        if (!isset($json['json']['state'])) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'State\' due to missing values.');
        }

        return new self($json['json']['state']);
    }

    /**
     * Return a JSON serialized representation
     */
    public function jsonSerialize(): array
    {
        return [
            'state'      => $this->state,
            'parameters' => $this->parameters
        ];
    }

    public function isSectionSet(AptoUuid $sectionId, int $repetition = 0): bool
    {
        foreach ($this->state as $state) {
            if ($state['sectionId'] === $sectionId->getId() && $state['repetition'] === $repetition) {
                return true;
            }
        }
        return false;
    }

    public function isElementSet(AptoUuid $elementId, int $repetition = 0): bool
    {
        foreach ($this->state as $state) {
            if ($state['elementId'] === $elementId->getId() && $state['repetition'] === $repetition) {
                return true;
            }
        }
        return false;
    }

    public function isItemSet(AptoUuid $sectionId, AptoUuid $elementId, int $repetition = 0): bool {
        foreach ($this->state as $state) {
            if ($state['sectionId'] === $sectionId->getId() &&
                $state['elementId'] === $elementId->getId() &&
                $state['repetition'] === $repetition) {
                return true;
            }
        }
        return false;
    }

    public function isElementValuesSet(AptoUuid $elementId, int $repetition = 0): bool
    {
        foreach ($this->state as $state) {
            if ($state['elementId'] === $elementId->getId() && $state['repetition'] === $repetition) {
                return !empty($state['elementId']['values']);
            }
        }
        return false;
    }

    public function getElementIds(): array {
        $elementIds = [];
        foreach ($this->state as $state) {
            if (!$this->isParameter($state['sectionId'])) {
                $elementIds[] = $state['elementId'];
            }
        }
        return $elementIds;
    }

    /**
     * Get all section items (repetitions) that match to the given section id and element id
     *
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     *
     * @return array
     */
    public function getElementRepetitions(AptoUuid $sectionId, AptoUuid $elementId): array
    {
        $repetitions = [];
        foreach ($this->state as $stateItem) {
            if ($stateItem['sectionId'] === $sectionId->getId() && $stateItem['elementId'] === $elementId->getId()
            ) {
                $repetitions[] = $stateItem;
            }
        }
        return $repetitions;
    }

    private function unsetStateItems(array $keys): void {
        if (!empty($keys)) {
            foreach ($keys as $key) {
                unset($this->state[$key]);
            }
        }
    }
}
