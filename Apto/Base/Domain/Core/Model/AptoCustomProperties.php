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
     * @param AptoCustomProperty $property
     * @return self
     */
    protected function setAptoCustomProperty(AptoCustomProperty $property): self
    {
        $key = $property->getKey();
        if ($this->customProperties->containsKey($key)) {
            // if property already exists, change value of this instance
            $existingProperty = $this->customProperties->get($key);
            $existingProperty->setValue($property->getValue());
            $existingProperty->setTranslatable($property->getTranslatable());
        } else {
            // create a new instance
            $this->customProperties->set($property->getKey(), $property);
        }
        return $this;
    }

    /**
     * @param AptoCustomProperty $property
     * @return self
     */
    protected function removeAptoCustomProperty(AptoCustomProperty $property): self
    {
        $this->customProperties->remove($property->getKey());
        return $this;
    }

    /**
     * @param string $key
     * @return AptoCustomProperty|null
     */
    protected function getAptoCustomProperty(string $key)
    {
        return $this->customProperties->get($key);
    }

    /**
     * @param AptoCustomProperty $property
     * @return bool
     */
    protected function hasAptoCustomProperty(AptoCustomProperty $property): bool
    {
        return $this->customProperties->containsKey($property->getKey());
    }

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
            new AptoCustomProperty($key, $value, $translatable)
        );
        return $this;
    }

    /**
     * @param string $key
     * @return self
     */
    public function removeCustomProperty(string $key): self
    {
        $this->customProperties->remove($key);
        return $this;
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function getCustomProperty(string $key)
    {
        $property = $this->getAptoCustomProperty($key);
        return null === $property ? null : $property->getValue();
    }

    /**
     * Attention: this method matches by key only
     * @param string $key
     * @return bool
     */
    public function hasCustomProperty(string $key): bool
    {
        return $this->customProperties->containsKey($key);
    }

    /**
     * @return bool
     */
    public function hasCustomProperties(): bool
    {
        return !$this->customProperties->isEmpty();
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
            $property = $customProperty->copy();

            $collection->set(
                $property->getKey(),
                $property
            );
        }

        return $collection;
    }
}
