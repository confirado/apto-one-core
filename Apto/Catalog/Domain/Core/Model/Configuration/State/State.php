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
        foreach (array_keys($state) as $section) {

            if (is_array($state[$section])) {
                foreach (array_keys($state[$section]) as $element) {
                    if (empty($state[$section][$element])) {
                        unset($state[$section][$element]);
                    }
                }
            }

            if (empty($state[$section])) {
                unset($state[$section]);
            }

        }

        $this->state = $state;
    }

    /**
     * @param string $parameter
     * @return mixed
     */
    public function getParameter(string $parameter)
    {
        // assert valid parameter
        if (!array_key_exists($parameter, self::PARAMETERS)) {
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
     * @return bool
     */
    public function isSectionActive(AptoUuid $sectionId): bool
    {
        $section = $sectionId->getId();

        return array_key_exists($section, $this->state);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return bool
     */
    public function isElementActive(AptoUuid $sectionId, AptoUuid $elementId): bool
    {
        $section = $sectionId->getId();
        $element = $elementId->getId();

        return
            array_key_exists($section, $this->state) &&
            array_key_exists($element, $this->state[$section]);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @return bool
     */
    public function isPropertyActive(AptoUuid $sectionId, AptoUuid $elementId, string $property): bool
    {
        $section = $sectionId->getId();
        $element = $elementId->getId();

        return
            array_key_exists($section, $this->state) &&
            array_key_exists($element, $this->state[$section]) &&
            $this->state[$section][$element] !== true &&
            array_key_exists($property, $this->state[$section][$element]);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return array
     */
    public function getValues(AptoUuid $sectionId, AptoUuid $elementId): ?array
    {
        $section = $sectionId->getId();
        $element = $elementId->getId();

        if (
            !array_key_exists($section, $this->state) ||
            !array_key_exists($element, $this->state[$section]) ||
            true === $this->state[$section][$element]
        ) {
            return null;
        }

        return $this->state[$section][$element];
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @return mixed|null
     */
    public function getValue(AptoUuid $sectionId, AptoUuid $elementId, string $property)
    {
        $section = $sectionId->getId();
        $element = $elementId->getId();

        return $this->state[$section][$element][$property] ?? null;
    }

    /**
     * @return array
     */
    public function getSectionList()
    {
        $sectionList = [];
        foreach ($this->state as $sectionId => $section) {
            if (!array_key_exists($sectionId, self::PARAMETERS)) {
                $sectionList[$sectionId] = true;
            }
        }
        return $sectionList;
    }

    /**
     * @return array
     */
    public function getElementList()
    {
        $elementList = [];
        foreach ($this->state as $sectionId => $section) {
            if (!array_key_exists($sectionId, self::PARAMETERS)) {
                $elementList = array_merge($elementList, $section);
            }
        }
        return $elementList;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string|null $property
     * @param mixed|null $value
     * @return State
     */
    public function setValue(AptoUuid $sectionId, AptoUuid $elementId, string $property = null, $value = null): State
    {
        $section = $sectionId->getId();
        $element = $elementId->getId();

        // create branch if needed
        if (!key_exists($section, $this->state)) {
            $this->state[$section] = [];
        }
        if (!key_exists($element, $this->state[$section])) {
            $this->state[$section][$element] = [];
        }

        if (null !== $property) {
            $this->state[$section][$element][$property] = $value;
        } else {
            $this->state[$section][$element] = true;
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid|null $elementId
     * @return State
     * @notice unset does not throw errors for non existing vars
     */
    public function removeValue(AptoUuid $sectionId, AptoUuid $elementId = null): State
    {
        $section = $sectionId->getId();
        if ($elementId) {
            $element = $elementId->getId();
            unset($this->state[$section][$element]);

            // remove also the section if that element was the last one in that section
            if (array_key_exists($section, $this->state) && count($this->state[$section]) === 0) {
                unset($this->state[$section]);
            }
        } else {
            unset($this->state[$section]);
        }

        // @TODO check, whether single properties might be removed separately, is this needed anyhow on clientside?

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @return State
     * @internal param string $property
     */
    public function removeSection(AptoUuid $sectionId): State
    {
        return $this->removeValue($sectionId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return State
     * @internal param string $property
     */
    public function removeElement(AptoUuid $sectionId, AptoUuid $elementId): State
    {
        return $this->removeValue($sectionId, $elementId);
    }

    /**
     * @param AptoUuid $sectionId
     * @return State
     * @deprecated use removeValue instead
     * @internal param string $property
     */
    public function removeValues(AptoUuid $sectionId): State
    {
        return $this->removeValue($sectionId);
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
     * @return array|bool|null
     */
    public function getElementState(AptoUuid $sectionId, AptoUuid $elementId)
    {
        if (!$this->isElementActive($sectionId, $elementId)) {
            return null;
        }

        return $this->state[$sectionId->getId()][$elementId->getId()];
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

}
