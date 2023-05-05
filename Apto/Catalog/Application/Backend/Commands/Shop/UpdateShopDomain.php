<?php

namespace Apto\Catalog\Application\Backend\Commands\Shop;

use Apto\Base\Application\Core\CommandInterface;

class UpdateShopDomain implements CommandInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $domain;

    /**
     * @param string $id
     * @param string $domain
     */
    public function __construct(string $id, string $domain)
    {
        $this->id = $id;
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
}
