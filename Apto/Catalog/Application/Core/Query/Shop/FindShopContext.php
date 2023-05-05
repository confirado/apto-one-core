<?php

namespace Apto\Catalog\Application\Core\Query\Shop;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindShopContext implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $domain;

    /**
     * FindShopByDomain constructor.
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
}