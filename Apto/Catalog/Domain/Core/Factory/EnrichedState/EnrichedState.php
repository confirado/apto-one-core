<?php

namespace Apto\Catalog\Domain\Core\Factory\EnrichedState;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class EnrichedState implements \JsonSerializable
{
    /**
     * @var State
     */
    protected $state;

    /**
     * "disabled" => [
     *    "(sectionId) b11e0bdb-be88-4411-a82d-5961228acfae" => [
     *       "(repetition) 0" => [
     *          "(elementId) ac314161-4b65-4ced-9475-10dee56d01ea" => true,
     *          "(elementId) ac314161-4b65-4ced-9475-10dee56d01ll" => false,
     *       ],
     *       "repetition 1" ...
     *       "repetition 2" ...
     *    ],
     *    "sectionId" => [
     *      "repetition 0" => [
     *          elementId,
     *          ...
     *      ],
     *      "repetition 1" => [
     *          elementId,
     *          ...
     *      ],
     *      ...
     *    ]
     * ]
     *
     * @var array
     */
    protected $disabled;

    /**
     * The structure is similar to "disabled"
     *
     * @var array
     */
    protected $complete;

    /**
     * @param State|null $state
     */
    public function __construct(State $state = null)
    {
        $this->state = $state ?? (new State());
        $this->disabled = [];
        $this->complete = [];
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * Repetition sections now can be enabled or disabled separate from each other
     *
     * @param AptoUuid $sectionId
     * @param array    $elementIds
     * @param int      $repetition
     *
     * @return bool
     */
    public function isSectionDisabled(AptoUuid $sectionId, array $elementIds, int $repetition = 0): bool
    {
        foreach ($elementIds as $elementId) {
            // The section cannot be considered as disabled if it contains even one enabled element
            if (!$this->isElementDisabled($sectionId, $elementId, $repetition)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param AptoUuid $sectionUuId
     * @param AptoUuid $elementUuId
     * @param int      $repetition
     *
     * @return bool
     */
    public function isElementDisabled(AptoUuid $sectionUuId, AptoUuid $elementUuId, int $repetition = 0): bool
    {
        $sectionId = $sectionUuId->getId();
        $elementId = $elementUuId->getId();

        return $this->disabled[$sectionId][$repetition][$elementId] ?? false;
    }

    /**
     * @param AptoUuid $sectionUuId
     * @param AptoUuid $elementUuId
     * @param int      $repetition
     * @param bool     $disabled
     *
     * @return $this
     */
    public function setElementDisabled(AptoUuid $sectionUuId, AptoUuid $elementUuId, bool $disabled = true, int $repetition = 0): self
    {
        $sectionId = $sectionUuId->getId();
        $elementId = $elementUuId->getId();

        if ($disabled) {
            // create branch if needed
            if (!array_key_exists($sectionId, $this->disabled)) {
                $this->disabled[$sectionId] = [];
            }

            if (!isset($this->disabled[$sectionId][$repetition])) {
                $this->disabled[$sectionId][$repetition] = [];
            }

            $this->disabled[$sectionId][$repetition][$elementId] = true;
        } else {
            unset($this->disabled[$sectionId][$repetition][$elementId]);

            // if no elements in that repetition anymore
            if (isset($this->disabled[$sectionId][$repetition]) && count($this->disabled[$sectionId][$repetition]) < 1) {
                unset($this->disabled[$sectionId][$repetition]);

                // if no other repetitions in that section then make the whole section not disabled
                if (isset($this->disabled[$sectionId]) && count($this->disabled[$sectionId]) < 1) {
                    unset($this->disabled[$sectionId]);
                }
            }

            // if no other repetitions in that section then make the whole section not disabled
            if (isset($this->disabled[$sectionId]) && count($this->disabled[$sectionId]) < 1) {
                unset($this->disabled[$sectionId]);
            }
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param array    $elementIds
     * @param bool     $allowMultiple
     * @param bool     $isMandatory
     * @param int      $repetition
     *
     * @return bool
     */
    public function isSectionComplete(AptoUuid $sectionId, array $elementIds, bool $allowMultiple, bool $isMandatory, int $repetition = 0): bool
    {
        $section = $sectionId->getId();

        // not selected mandatory sections can not be complete
        if ($isMandatory && !$this->state->isSectionActive($sectionId, $repetition)) {
            return false;
        }

        // multi-select sections can be completed manually
        if ($this->complete[$section][$repetition] ?? false) {
            return true;
        }

        // single select sections are complete, if at least one element is active
        if (!$allowMultiple) {
            return $this->state->isSectionActive($sectionId, $repetition);
        }

        // multi-select sections are automatically completed if all contained elements are active
        foreach ($elementIds as $elementId) {
            if (!$this->state->isElementActive($sectionId, $elementId, $repetition)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param AptoUuid $sectionUuId
     * @param bool     $complete
     * @param int      $repetition
     *
     * @return $this
     */
    public function setSectionComplete(AptoUuid $sectionUuId, bool $complete = true, int $repetition = 0): self
    {
        $sectionId = $sectionUuId->getId();

        if ($complete) {
            $this->complete[$sectionId][$repetition] = true;
        } else {
            unset ($this->complete[$sectionId][$repetition]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'state' => $this->state,
            'disabled' => $this->disabled,
            'complete' => $this->complete
        ];
    }
}
