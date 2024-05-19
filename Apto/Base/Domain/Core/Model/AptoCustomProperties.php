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
     * @return self
     * @throws AptoCustomPropertyException
     */
    public function setCustomProperty(string $key, string $value, bool $translatable = false): self
    {
        //@todo always make a new instance of an domain model also if it already exists is evil, keyword: doctrine entity manager(maybe persists this instance even if its not a new one in 'setAptoCustomProperty'), domain events
        $this->setAptoCustomProperty(
            new AptoCustomProperty(
                $this->nextAptoCustomPropertyId(),
                $key,
                $value,
                $translatable
            )
        );
        return $this;
    }

    /**
     * @param AptoCustomProperty $property
     * @return self
     */
    protected function setAptoCustomProperty(AptoCustomProperty $property): self
    {
        $key = $property->getId()->getId();
        if ($this->customProperties->containsKey($key)) {
            // if property already exists, change value of this instance
            $existingProperty = $this->customProperties->get($key);
            $existingProperty->setValue($property->getValue());
            $existingProperty->setTranslatable($property->getTranslatable());
        } else {
            // create a new instance
            $this->customProperties->set($key, $property);
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
     * @return string|null
     */
    public function getCustomPropertyValueByKey(string $key): ?string
    {
        /** @var AptoCustomProperty $customProperty */
        foreach ($this->customProperties as $customProperty) {
            if ($customProperty->getKey() === $key) {
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
     * @return AptoUuid
     */
    protected function nextAptoCustomPropertyId(): AptoUuid
    {
        return new AptoUuid();
    }
}
