<?php

namespace Apto\Base\Domain\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait AptoCustomProperties
{
    /**
     * @var Collection
     */
    protected $customProperties;

    /**
     * @param string $key
     * @param string $value
     * @param bool $translatable
     * @param AptoUuid|null $productConditionId
     * @return self
     * @throws AptoCustomPropertyException
     */
    public function setCustomProperty(string $key, string $value, bool $translatable = false, ?AptoUuid $productConditionId = null): self
    {
        // property exists?
        $property = $this->getCustomPropertyByKeyAndConditionId($key, $productConditionId ? $productConditionId->getId() : $productConditionId);

        // update property or create property
        if (null === $property) {
            $property = new AptoCustomProperty(
                $this->nextAptoCustomPropertyId(),
                $key,
                $value,
                $translatable,
                $productConditionId
            );
            $this->customProperties->set($property->getId()->getId(), $property);
        } else {
            $property->setValue($value);
            $property->setTranslatable($translatable);
        }

        return $this;
    }

    /**
     * @param AptoUuid $id
     * @return string|null
     */
    public function getCustomPropertyValue(AptoUuid $id): ?string
    {
        $property = $this->customProperties->get($id->getId());
        return null === $property ? null : $property->getValue();
    }

    /**
     * @param string $key
     * @param AptoUuid|null $productConditionId
     * @return string|null
     */
    public function getCustomPropertyValueByKey(string $key, ?AptoUuid $productConditionId = null): ?string
    {
        $pConditionId = $productConditionId?->getId();

        /** @var AptoCustomProperty $customProperty */
        foreach ($this->customProperties as $customProperty) {
            $cpConditionId = $customProperty->getProductConditionId()?->getId();
            if ($customProperty->getKey() === $key && $cpConditionId === $pConditionId) {
                return $customProperty->getValue();
            }
        }
        return null;
    }

    /**
     * @param AptoUuid $id
     * @return self
     */
    public function removeCustomProperty(AptoUuid $id): self
    {
        $this->customProperties->remove($id->getId());
        return $this;
    }

    /**
     * @return self
     */
    public function clearCustomProperties(): self
    {
        $this->customProperties->clear();
        return $this;
    }

    /**
     * @return Collection
     * @throws AptoCustomPropertyException
     */
    protected function copyAptoCustomProperties(): Collection
    {
        $collection = new ArrayCollection();

        /** @var AptoCustomProperty $customProperty */
        foreach ($this->customProperties as $customProperty) {
            $id = $this->nextAptoCustomPropertyId();

            $collection->set(
                $id->getId(),
                $customProperty->copy($id)
            );
        }

        return $collection;
    }

    /**
     * @param string $key
     * @param string|null $conditionId
     * @return AptoCustomProperty|null
     */
    private function getCustomPropertyByKeyAndConditionId(string $key, ?string $conditionId): ?AptoCustomProperty
    {
        /** @var AptoCustomProperty $customProperty */
        foreach ($this->customProperties as $customProperty) {
            $productConditionId = $customProperty->getProductConditionId() ? $customProperty->getProductConditionId()->getId() : $customProperty->getProductConditionId();
            if ($customProperty->getKey() === $key && $productConditionId === $conditionId) {
                return $customProperty;
            }
        }
        return null;
    }

    /**
     * @return AptoUuid
     */
    private function nextAptoCustomPropertyId(): AptoUuid
    {
        return new AptoUuid();
    }
}
