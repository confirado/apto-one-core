<?php

namespace Apto\Catalog\Application\Backend\Commands\Shop;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddShop implements CommandInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var array|null
     */
    private $connectorUrl;

    /**
     * @var string|null
     */
    private $connectorToken;

    /**
     * @var string|null
     */
    private $templateId;

    /**
     * @var string|null
     */
    private $operatorName;

    /**
     * @var string|null
     */
    private $operatorEmail;

    /**
     * @var string|null
     */
    private $currency;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var array
     */
    private $languages;

    /**
     * AddShop constructor.
     * @param string $name
     * @param string $domain
     * @param array|null $connectorUrl
     * @param string|null $connectorToken
     * @param string|null $templateId
     * @param string|null $currency
     * @param string|null $description
     * @param array $categories
     * @param array $languages
     * @param string|null $operatorName
     * @param string|null $operatorEmail
     */
    public function __construct(
        string $name,
        string $domain,
        array $connectorUrl = null,
        string $connectorToken = null,
        string $templateId = null,
        string $currency = null,
        string $description = null,
        array $categories = [],
        array $languages = [],
        string $operatorName = null,
        string $operatorEmail = null
    )
    {
        $this->name = $name;
        $this->domain = $domain;
        $this->connectorUrl = $connectorUrl;
        $this->connectorToken = $connectorToken;
        $this->templateId = $templateId;
        $this->operatorName = $operatorName;
        $this->operatorEmail = $operatorEmail;
        $this->currency = $currency;
        $this->description = $description;
        $this->categories = $categories;
        $this->languages = $languages;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return array|null
     */
    public function getConnectorUrl()
    {
        return $this->connectorUrl;
    }

    /**
     * @return string|null
     */
    public function getConnectorToken()
    {
        return $this->connectorToken;
    }

    /**
     * @return null|string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @return null|string
     */
    public function getOperatorName()
    {
        return $this->operatorName;
    }

    /**
     * @return null|string
     */
    public function getOperatorEmail()
    {
        return $this->operatorEmail;
    }

    /**
     * @return null|string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
}