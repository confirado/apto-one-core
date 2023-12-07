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

    /**
     * list of all parameters with their default values
     */
    const PARAMETERS = [
        self::QUANTITY => 1,
        self::IGNORED_RULES => []
    ];

    /**
     * @var array
     */
    protected $state;

    /**
     * State constructor.
     * @param array $state
     */
    public function __construct(array $state = [])
    {
        // remove empty branches
        $keysToRemove = [];
        foreach ($state as $key => $st) {
            if (empty($st['values'])) {
                $keysToRemove[] = $key;
            }
        }
        $this->unsetStateItems($keysToRemove);

        $this->state = $state;
    }

    public function isParameter(string $stateItem): bool
    {
        return array_key_exists($stateItem, self::PARAMETERS);
    }

    /**
     * @param string $parameter
     * @return mixed
     */
    public function getParameter(string $parameter)
    {
        // @todo maybe save 'parametes' in a separate class property

        // assert valid parameter
        if (!$this->isParameter($parameter)) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" is not a valid parameter.',
                $parameter
            ));
        }

        // return state value
        if (array_key_exists($parameter, $this->state)) {
            return $this->state[$parameter];
        }

        // return default value
        return self::PARAMETERS[$parameter];
    }

    /**
     * Apply all missing parameters with their default values to given state
     * @param array $state
     * @return array
     */
    protected function applyMissingParameters(array $state): array
    {
        // @todo maybe save 'parametes' in a separate class property

        foreach (self::PARAMETERS as $parameter => $defaultValue) {
            // set default value for parameter, if it does not exist
            if (!array_key_exists($parameter, $state)) {
                $state[$parameter] = $defaultValue;
            }
        }

        return $state;
    }

    /**
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
     * In many cases, we can skip repetition argument as all repetitions are activated or deactivated together
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
     * @param AptoUuid    $sectionId
     * @param AptoUuid    $elementId
     * @param string|null $property is null on default element, or when element has no properties at all (has no selectable values in element definition)
     * @param mixed|null  $value
     * @param int         $repetition
     *
     * @return State
     */
    public function setValue(AptoUuid $sectionId, AptoUuid $elementId, string $property = null, $value = null, int $repetition = 0): State
    {
        // if an element isn't found in the state
        if (!$this->isElementActive($sectionId, $elementId, $repetition)) {
            $this->state[] = [
                'repetition' => $repetition,
                'sectionId' => $sectionId->getId(),
                'elementId' => $elementId->getId(),
                'values' => $property !== null ? [$property => $value] : []
            ];
        }
        else {
            // then let's find the element and set its value
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
        return $this->applyMissingParameters($this->state);
    }

    /**
     * Return raw state without parameters
     * @return array
     */
    public function getStateWithoutParameters(): array
    {
        $state = $this->state;

        foreach (self::PARAMETERS as $parameter => $defaultValue) {
            unset($state[$parameter]);
        }

        return $state;
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
        return $this->state;
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
