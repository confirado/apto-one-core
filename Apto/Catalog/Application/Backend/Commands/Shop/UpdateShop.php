<?php

namespace Apto\Catalog\Application\Backend\Commands\Shop;

class UpdateShop extends AbstractAddShop
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateShop constructor.
     * @param string $id
     * @param string $name
     * @param string $domain
     * @param array $connectorUrl
     * @param string $connectorToken
     * @param string|null $templateId
     * @param string|null $currency
     * @param string|null $description
     * @param array $categories
     * @param array $languages
     * @param string|null $operatorName
     * @param string|null $operatorEmail
     */
    public function __construct(
        string $id,
        string $name,
        string $domain,
        array $connectorUrl = null,
        string $connectorToken = null,
        string $templateId = null,
        string $currency = null,
        $description = null,
        array $categories = [],
        array $languages = [],
        string $operatorName = null,
        string $operatorEmail = null
    )
    {
        parent::__construct($name, $domain, $connectorUrl, $connectorToken, $templateId, $currency, $description, $categories, $languages, $operatorName, $operatorEmail);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}