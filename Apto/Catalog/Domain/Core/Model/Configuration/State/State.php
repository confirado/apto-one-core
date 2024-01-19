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
     * Configuration state for element configs (internally configuration configs are saved here)
     *
     *
     * @var array
     */
    protected array $state;

    /**
     * Holds the parameters that are saved in state and have some value but not directly related to element configs
     *
     * Example data:
     * [
     *    [
     *       'name' => 'quantity',
     *       'value' => 1
     *    ],
     *    [
     *       'name' => 'repetitions',
     *       'value' => 1
     *    ],
     * ];
     *
     * @var array
     */
    protected array $parameters;

    /**
     * Incoming state:
     *
     * Example data within the class (outside the class it can be different):
     *
     *  [
     *     [
     *        'sectionId' => 'id...',   |
     *        'elementId' => 'id...',   |
     *        'repetition' => 1,        |  -> configuration config
     *        'values' => [],           |
     *     ],
     *     [
     *        'name' => 'quantity',     | -> parameter config
     *        'value' => 1              |
     *     ],
     *     [
     *        'name' => 'repetitions',
     *        'value' => 1
     *     ],
     *  ];
     *
     * @param array $state An array with keys 'state' and 'properties', each containing an array.
     */
    public function __construct(array $state = [])
    {
        $this->state = [];
        $this->parameters = [];

        if (!empty($state)) {

            /*  We want to divide configurations from parameters, and then from configuration delete those items
                that have no values. So we remove empty branches from state...   */
            foreach ($state as $configItem) {
                if ($this->isParameter($configItem)) {
                    $this->parameters[] = $configItem;
                }
                else {
                    $this->state[] = $configItem;
                }
            }
        }

        $this->applyMissingParameters();
    }

    /**
     * Checks if the given array is parameter state array (see $state and $parameter declarations)
     *
     * Parameters are special cases that exit in state, but are not directly linked to product, product section
     * and/or product elements. Therefor we need to handle them separately
     *
     * for example quantity of selected items is parameter
     *
     * @param array $stateItem
     *
     * @return bool
     */
    private function isParameter(array $stateItem): bool
    {
        foreach (array_keys($stateItem) as $key) {
            if ($key === 'name' && $this->isValidParameterName($stateItem[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the given string is one of the parameter names
     *
     * @param string $name
     *
     * @return bool
     */
    private function isValidParameterName(string $name): bool
    {
        return array_key_exists($name, self::PARAMETERS);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasParameter(string $name): bool
    {
        foreach ($this->parameters as $parameter) {
            if ($parameter['name'] === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get parameter value
     *
     * if parameter value is set returns it's value, otherwise returns the default value
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter(string $name): mixed
    {
        if (!$this->isValidParameterName($name)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid parameter.', $name));
        }

        foreach ($this->parameters as $parameter) {
            if ($parameter['name'] === $name) {
                return $parameter['value'];
            }
        }

        throw new \InvalidArgumentException(sprintf('"%s" parameter not found.', $name));
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
        if (!$this->isValidParameterName($name)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid parameter.', $name));
        }

        foreach ($this->parameters as $key => $parameter) {
            if ($parameter['name'] === $name) {
                $this->parameters[$key]['value'] = $value;
            }
        }
    }

    /**
     * Returns all parameters that have set value (exist in state)
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Apply all missing parameters with their default values to the state
     *
     * if parameter exists we keep it's value, otherwise we take it's default value
     *
     * @return void
     */
    private function applyMissingParameters(): void
    {
        foreach (self::PARAMETERS as $parameter => $defaultValue) {
            if (!$this->hasParameter($parameter)) {
                $this->parameters[] = [
                    'name' => $parameter,
                    'value' => $defaultValue,
                ];
            }
        }
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
     * [
     *    [some_random_id] => true,
     *    [some_random_id] => true,
     *    [some_random_id] => true,
     * ]
     *
     * @return array
     */
    public function getSectionIds(): array
    {
        $sectionList = [];
        foreach ($this->state as $state) {
            $sectionList[$state['sectionId']] = true;
        }
        return $sectionList;
    }

    /**
     * Returns sections with its repetition
     *
     * @return array
     */
    public function getSectionList(): array
    {
        $sectionList = [];
        $lookup = [];

        foreach ($this->state as $state) {
            if (!array_key_exists($state['sectionId'] . $state['repetition'], $lookup)) {
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
            $elementList[] = $state;
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
     *
     * @return array
     */
    public function getState(): array
    {
        return array_merge($this->state, $this->parameters);
    }

    /**
     * Return raw state without parameters
     *
     * Within the class our configuration and parameter states are divided, but for the outer world they are together
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
     * We assume that this->state does not have parameters, but $state can be with parameters
     *
     * therefor we compare $state argument with $this->getState() which contains parameters as well
     *
     * @param State $state
     *
     * @return bool
     */
    public function equals(State $state): bool
    {
        return $this->getState() == $state->getState();
    }

    /**
     * Again everytime we read the state, it should contain parameters as well (thank to $this->getState())
     *
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
            'json' => [
                'state' => $this->getState()
            ]
        ];
    }

    /**
     * As we do "new self" this will contain parameters as well
     *
     * @param array $json
     *
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
     *
     * Get state with parameters, again when reading the state it should contain parameters
     */
    public function jsonSerialize(): array
    {
        return $this->getState();
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
            $elementIds[] = $state['elementId'];
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
