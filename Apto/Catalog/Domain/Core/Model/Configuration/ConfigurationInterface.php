<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Money\Currency;

use Apto\Base\Domain\Core\Model\InvalidUuidException;

interface ConfigurationInterface
{
    /**
     * @return Product
     */
    public function getProduct(): Product;

    /**
     * @param Product $product
     * @param State $state
     * @return mixed
     */
    public function setProductAndState(Product $product, State $state);

    /**
     * @return State
     */
    public function getState(): State;

    /**
     * @param State $state
     * @return mixed
     */
    public function setState(State $state);

    /**
     * @param AptoUuid $sectionId
     * @return bool
     */
    public function isSectionActive(AptoUuid $sectionId): bool;

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return bool
     */
    public function isElementActive(AptoUuid $sectionId, AptoUuid $elementId): bool;

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @return bool
     */
    public function isPropertyActive(AptoUuid $sectionId, AptoUuid $elementId, string $property): bool;

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @return mixed
     */
    public function getValue(AptoUuid $sectionId, AptoUuid $elementId, string $property);

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string|null $property
     * @param string|null $value
     * @return mixed
     */
    public function setValue(AptoUuid $sectionId, AptoUuid $elementId, string $property = null, string $value = null);

    /**
     * @param AptoUuid $sectionId
     * @return mixed
     */
    public function removeValues(AptoUuid $sectionId);

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getRenderImages(): array;

    /**
     * @param string $perspective
     * @return array
     * @throws InvalidUuidException
     */
    public function getRenderImagesByPerspective(string $perspective): array;

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function sortRenderImagesByLayer($a, $b);

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return mixed
     */
    public function getConfigurationPrices(Currency $currency, AptoUuid $customerGroupId);
}