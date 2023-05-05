<?php
namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\EmailOptional;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Money\Currency;

class Shop extends AptoAggregate
{
    use AptoCustomProperties;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var AptoTranslatedValue
     */
    protected $connectorUrl;

    /**
     * @var string
     */
    protected $connectorToken;

    /**
     * @var string
     */
    protected $templateId;

    /**
     * @var EmailOptional
     */
    protected $operatorEmail;

    /**
     * @var string
     */
    protected $operatorName;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var Collection
     */
    private $categories;

    /**
     * @var Collection
     */
    protected $products;

    /**
     * @var Collection
     */
    protected $languages;

    /**
     * Shop constructor.
     * @param AptoUuid $id
     * @param string $name
     * @param string $domain
     */
    public function __construct(AptoUuid $id, $name, $domain)
    {
        parent::__construct($id);
        $this->publish(
            new ShopAdded($id)
        );

        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->customProperties = new ArrayCollection();

        $this
            ->setName($name)
            ->setDomain($domain);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Shop
     */
    public function setName($name)
    {
        if($this->name == $name) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new ShopNameUpdated(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Shop
     */
    public function setDescription($description)
    {
        if($this->description == $description) {
            return $this;
        }
        $this->description = $description;
        $this->publish(
            new ShopDescriptionUpdated(
                $this->getId(),
                $this->getDescription()
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return Shop
     */
    public function setDomain($domain)
    {
        if ($this->domain == $domain) {
            return $this;
        }
        $this->domain = $domain;
        $this->publish(
            new ShopDomainUpdated(
                $this->getId(),
                $this->getDomain()
            )
        );
        return $this;
    }

    /**
     * @return AptoTranslatedValue|null
     */
    public function getConnectorUrl()
    {
        return $this->connectorUrl;
    }

    /**
     * @param AptoTranslatedValue|null $connectorUrl
     * @return Shop
     */
    public function setConnectorUrl(AptoTranslatedValue $connectorUrl = null): Shop
    {
        $doUpdate = false;

        if ((null === $connectorUrl || null === $this->connectorUrl) && !(null === $connectorUrl && null === $this->connectorUrl)) {
            $doUpdate = true;
        }

        if (null !== $connectorUrl && null !== $this->connectorUrl && !$this->connectorUrl->equals($connectorUrl)) {
            $doUpdate = true;
        }

        if (true === $doUpdate) {
            $this->connectorUrl = $connectorUrl;
            $this->publish(
                new ShopConnectorUrlUpdated(
                    $this->getId(),
                    $this->getConnectorUrl()
                )
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getConnectorToken(): string
    {
        return $this->connectorToken;
    }

    /**
     * @param string|null $connectorToken
     * @return Shop
     */
    public function setConnectorToken(string $connectorToken = null): Shop
    {
        if ($this->connectorToken == $connectorToken) {
            return $this;
        }
        $this->connectorToken = $connectorToken;
        $this->publish(
            new ShopConnectorTokenUpdated(
                $this->getId(),
                $this->getConnectorToken()
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateId(): string
    {
        return $this->templateId;
    }

    /**
     * @param string $templateId
     * @return Shop
     */
    public function setTemplateId(string $templateId = null): Shop
    {
        if ($this->templateId == $templateId) {
            return $this;
        }
        $this->templateId = $templateId;
        $this->publish(
            new ShopTemplateIdUpdated(
                $this->getId(),
                $this->getTemplateId()
            )
        );
        return $this;
    }

    /**
     * @return EmailOptional
     */
    public function getOperatorEmail(): EmailOptional
    {
        return $this->operatorEmail;
    }

    /**
     * @param EmailOptional $operatorEmail
     * @return $this
     */
    public function setOperatorEmail(EmailOptional $operatorEmail): Shop
    {
        $this->operatorEmail = $operatorEmail;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOperatorName(): ?string
    {
        return $this->operatorName;
    }

    /**
     * @param string|null $operatorName
     * @return Shop
     */
    public function setOperatorName(?string $operatorName = null): Shop
    {
        if ($this->operatorName == $operatorName) {
            return $this;
        }

        $this->operatorName = $operatorName;
        $this->publish(
            new ShopOperatorNameUpdated(
                $this->getId(),
                $this->getOperatorName()
            )
        );
        return $this;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     * @return Shop
     */
    public function setCurrency(Currency $currency): Shop
    {
        if (null !== $this->currency && $this->currency->equals($currency)) {
            return $this;
        }
        $this->currency = $currency;
        $this->publish(
            new ShopCurrencyUpdated(
                $this->getId(),
                $this->getCurrency()
            )
        );
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection $categories
     * @return Shop
     */
    public function setCategories(Collection $categories)
    {
        if (!$this->hasCollectionChanged($this->getCategories(), $categories)) {
            return $this;
        }

        $this->categories = $categories;
        $this->publish(
            new ShopCategoriesUpdated(
                $this->getId(),
                $this->getCollectionIds($this->getCategories())
            )
        );
        return $this;
    }

    /**
     * @return Collection
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    /**
     * @param Collection $languages
     * @return $this
     */
    public function setLanguages(Collection $languages)
    {
        if (!$this->hasCollectionChanged($this->getLanguages(), $languages)) {
            return $this;
        }

        $this->languages = $languages;
        $this->publish(
            new ShopLanguagesUpdated(
                $this->getId(),
                $this->getCollectionIds($this->getLanguages())
            )
        );
        return $this;
    }

    /**
     * @return Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
