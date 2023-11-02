<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Money\Currency;

class Configuration extends AptoAggregate implements ConfigurationInterface
{
    use AptoCustomProperties;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var State
     */
    protected $state;

    /**
     * Configuration constructor.
     * @param AptoUuid $id
     * @param Product $product
     * @param State $state
     */
    public function __construct(AptoUuid $id, Product $product, State $state)
    {
        parent::__construct($id);
        $this->product = $product;
        $this->state = $state;
        $this->customProperties = new ArrayCollection();
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @param State $state
     * @return $this
     */
    public function setProductAndState(Product $product, State $state): self
    {
        $this->product = $product;
        $this->setState($state);
        return $this;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState(State $state)
    {
        $this->state = $state;
    }

    /**
     * @param AptoUuid $sectionId
     * @return bool
     */
    public function isSectionActive(AptoUuid $sectionId): bool
    {
        return $this->state->isSectionActive($sectionId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return bool
     */
    public function isElementActive(AptoUuid $sectionId, AptoUuid $elementId): bool
    {
        return $this->state->isElementActive($sectionId, $elementId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @return bool
     */
    public function isPropertyActive(AptoUuid $sectionId, AptoUuid $elementId, string $property): bool
    {
        return $this->state->isPropertyActive($sectionId, $elementId, $property);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @return mixed
     */
    public function getValue(AptoUuid $sectionId, AptoUuid $elementId, string $property)
    {
        return $this->state->getValue($sectionId, $elementId, $property);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string|null $property
     * @param string|null $value
     * @return Configuration
     */
    public function setValue(AptoUuid $sectionId, AptoUuid $elementId, string $property = null, string $value = null): Configuration
    {
        $this->state->setValue($sectionId, $elementId, $property, $value);
        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @return Configuration
     */
    public function removeValues(AptoUuid $sectionId): Configuration
    {
        $this->state->removeValue($sectionId);
        return $this;
    }

    /**
     * @return array
     * @throws InvalidUuidException
     */
    public function getRenderImages(): array
    {
        $renderImages = [];
        foreach ($this->state->getStateWithoutParameters() as $sectionItem) {
            $sectionUuid = new AptoUuid($sectionItem['sectionId']);
            $elementUuid = new AptoUuid($sectionItem['elementId']);
            $renderImages = array_merge_recursive($renderImages, $this->getProduct()->getElementRenderImages($sectionUuid, $elementUuid));
        }

        foreach ($renderImages as &$perspective) {
            usort($perspective, array(self::class, 'sortRenderImagesByLayer'));
        }

        return $renderImages;
    }

    /**
     * @param string $perspective
     * @return array
     * @throws InvalidUuidException
     */
    public function getRenderImagesByPerspective(string $perspective): array
    {
        $renderImages = [];
        foreach ($this->state->getStateWithoutParameters() as $sectionItem) {
            $sectionUuid = new AptoUuid($sectionItem['sectionId']);
            $elementUuid = new AptoUuid($sectionItem['elementId']);
            $renderImages = array_merge_recursive($renderImages, $this->getProduct()->getElementRenderImagesByPerspective($sectionUuid, $elementUuid, $perspective));
        }

        usort($renderImages, array(self::class, 'sortRenderImagesByLayer'));

        return $renderImages;
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function sortRenderImagesByLayer($a, $b)
    {
        if ($a['layer'] == $b['layer']) {
            return 0;
        }
        return ($a['layer'] < $b['layer']) ? -1 : 1;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return array
     * @throws InvalidUuidException
     */
    public function getConfigurationPrices(Currency $currency, AptoUuid $customerGroupId)
    {
        return [
            'productPrices' => $this->getProductPrices($currency, $customerGroupId),
            'sectionPrices' => $this->getSectionPrices($currency, $customerGroupId),
            'elementPrices' => $this->getElementPrices($currency, $customerGroupId)
        ];
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return array
     */
    private function getProductPrices(Currency $currency, AptoUuid $customerGroupId)
    {
        $productPrice = $this->getProduct()->getAptoPrice($currency, $customerGroupId);

        if (null === $productPrice) {
            return [];
        }

        return [
            $this->getProduct()->getId()->getId() => $productPrice
        ];
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return array
     * @throws InvalidUuidException
     */
    private function getSectionPrices(Currency $currency, AptoUuid $customerGroupId)
    {
        $result = [];
        foreach ($this->state->getStateWithoutParameters() as $sectionItem) {
            $sectionIdValue = new AptoUuid($sectionItem['sectionId']);

            $sectionPrice = $this->getProduct()->getSectionPrice(
                $sectionIdValue,
                $currency,
                $customerGroupId
            );

            if (null !== $sectionPrice) {
                $result[$sectionItem['sectionId']] = $sectionPrice;
            }
        }
        return $result;
    }

    /**
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return array
     * @throws InvalidUuidException
     */
    private function getElementPrices(Currency $currency, AptoUuid $customerGroupId)
    {
        $result = [];
        foreach ($this->state->getStateWithoutParameters() as $sectionItem) {
            $sectionIdValue = new AptoUuid($sectionItem['sectionId']);
            $elementIdValue = new AptoUuid($sectionItem['elementId']);

            $elementPrice = $this->getProduct()->getElementPrice(
                $sectionIdValue,
                $elementIdValue,
                $currency,
                $customerGroupId
            );

            if (null !== $elementPrice) {
                $result[$sectionItem['elementId']] = $elementPrice;
            }
        }
        return $result;
    }
}
