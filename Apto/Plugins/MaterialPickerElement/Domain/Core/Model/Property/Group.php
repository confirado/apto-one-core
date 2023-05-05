<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Group extends AptoAggregate
{
    /**
     * @var Collection
     */
    protected $properties;

    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * @var bool
     */
    protected $allowMultiple;

    /**
     * Group constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->properties = new ArrayCollection();
        $this->name = $name;
        $this->allowMultiple = false;
        $this->publish(
            new GroupAdded(
                $this->getId(),
                $name
            )
        );
    }

    /**
     * @param AptoTranslatedValue $name
     * @return Group
     */
    public function setName(AptoTranslatedValue $name): Group
    {
        if ($this->name->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new GroupNameChanged(
                $this->getId(),
                $name
            )
        );

        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @param AptoTranslatedValue $name
     * @return Group
     */
    public function addProperty(AptoTranslatedValue $name): Group
    {
        $propertyId = $this->getNextPropertyId();
        $property = new Property($propertyId, $this, $name);
        $this->properties->set($propertyId->getId(), $property);
        $this->publish(
            new GroupPropertyAdded(
                $this->getId(),
                $propertyId,
                $name
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $propertyId
     * @return Group
     */
    public function removeProperty(AptoUuid $propertyId): Group
    {
        if ($this->properties->containsKey($propertyId->getId())) {
            $this->properties->remove($propertyId->getId());
            $this->publish(
                new GroupPropertyRemoved(
                    $this->getId(),
                    $propertyId
                )
            );
        }
        return $this;
    }

    /**
     * @param bool $allowMultiple
     * @return Group
     */
    public function setAllowMultiple(bool $allowMultiple): Group
    {
        if ($this->allowMultiple !== $allowMultiple) {
            $this->allowMultiple = $allowMultiple;
            $this->publish(
                new GroupAllowMultipleChanged(
                    $this->getId(),
                    $allowMultiple
                )
            );
        }

        return $this;
    }

    /**
     * @return AptoUuid
     */
    private function getNextPropertyId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @return bool
     */
    public function isAllowMultiple(): bool
    {
        return $this->allowMultiple;
    }

    /**
     * @return Collection
     */
    public function getProperties()
    {
        return $this->properties;
    }
}