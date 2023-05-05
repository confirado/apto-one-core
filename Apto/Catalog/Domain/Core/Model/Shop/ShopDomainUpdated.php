<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ShopDomainUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $domain;

    /**
     * ShopDomainUpdated constructor.
     * @param AptoUuid $id
     * @param string $domain
     */
    public function __construct(AptoUuid $id, $domain)
    {
        parent::__construct($id);
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
}