<?php
namespace Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;

class Alias extends AptoEntity
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string|null
     */
    private $elementId;

    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ComputedProductValue
     */
    private $computedProductValue;

    /**
     * @var bool
     */
    private $isCustomProperty;

    /**
     * @param AptoUuid $id
     * @param string $sectionId
     * @param string|null $elementId
     * @param ComputedProductValue $computedProductValue
     * @param string $name
     * @param string $property
     * @param bool $isCustomProperty
     * @throws InvalidAliasException
     */
    public function __construct(AptoUuid $id, string $sectionId, ?string $elementId, ComputedProductValue $computedProductValue, string $name, string $property = '', bool $isCustomProperty = false )
    {
        parent::__construct($id);
        $this->publish(
            new AliasAdded(
                $this->getId()
            )
        );
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->computedProductValue = $computedProductValue;
        $this->name = $name;
        $this->property = $property;
        $this->isCustomProperty = $isCustomProperty;

        $this->assertValidAlias();
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    /**
     * @param string $sectionId
     * @return Alias
     */
    public function setSectionId(string $sectionId): Alias
    {
        $this->sectionId = $sectionId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getElementId(): ?string
    {
        return $this->elementId;
    }

    /**
     * @param string|null $elementId
     * @return $this
     * @throws InvalidAliasException
     */
    public function setElementId(?string $elementId): Alias
    {
        $this->elementId = $elementId;
        $this->assertValidAlias();
        return $this;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @param string $property
     * @return $this
     * @throws InvalidAliasException
     */
    public function setProperty(string $property): Alias
    {
        $this->property = $property;
        $this->assertValidAlias();
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     * @throws InvalidAliasException
     */
    public function setName(string $name): Alias
    {
        $this->name = $name;
        $this->assertValidAlias();
        return $this;
    }

    /**
     * @return ComputedProductValue
     */
    public function getComputedProductValue(): ComputedProductValue
    {
        return $this->computedProductValue;
    }

    /**
     * @return bool
     */
    public function isCustomProperty(): bool
    {
        return $this->isCustomProperty;
    }

    /**
     * @param bool $isCustomProperty
     * @return $this
     * @throws InvalidAliasException
     */
    public function setIsCustomProperty(bool $isCustomProperty): Alias
    {
        $this->isCustomProperty = $isCustomProperty;
        $this->assertValidAlias();
        return $this;
    }

    /**
     * @param State $state
     * @param Product $product
     * @return float
     * @throws InvalidUuidException
     */
    public function getAliasValue(State $state, Product $product, int $repetition = 0): float
    {
        $sectionId = new AptoUuid($this->sectionId);

        // case: only section and element custom property is given
        if ($this->elementId === null && $this->isCustomProperty) {
            foreach ($product->getElementIds($sectionId) as $elementId) {
                if (!$state->isElementActive($sectionId, $elementId, $repetition)) {
                    continue;
                }
                return floatval(str_replace(',', '.', $product->getElement($sectionId, $elementId)->getCustomProperty($this->property)));
            }
            return floatval(0);
        }

        // cases for specific element
        $elementId = new AptoUuid($this->elementId);

        // case: specific element custom property is given
        if ($this->isCustomProperty) {
            if (!$state->isElementActive($sectionId, $elementId, $repetition)) {
                return 0;
            }

            return floatval(str_replace(',', '.', $product->getElement($sectionId, $elementId)->getCustomProperty($this->property)));
        }

        // case: element selectable value is given
        if ($this->property) {
            return floatval(str_replace(',', '.', $state->getValue($sectionId, $elementId, $this->property, $repetition)));
        }

        // no property is given, just check for active or not and return 1 or 0
        return floatval($state->isElementActive($sectionId, $elementId, $repetition));
    }

    /**
     * @return void
     * @throws InvalidAliasException
     */
    private function assertValidAlias(): void
    {
        if ($this->elementId === null && (!$this->property || $this->isCustomProperty === false)) {
            throw new InvalidAliasException('Either an elementId or a element custom property has to be supplied.');
        }

        if ($this->isCustomProperty === true && !$this->property) {
            throw new InvalidAliasException('If a property is a custom property a property must be given.');
        }

        if (!preg_match('/\b[a-zA-Z]\b/', $this->name) || $this->name === '' || $this->name === 'e') {
            throw new InvalidAliasException('The name must not be empty and can only contain one letter from a to z, e as alias name is not allowed');
        }
    }
}
