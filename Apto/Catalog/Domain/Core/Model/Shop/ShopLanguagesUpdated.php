<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ShopLanguagesUpdated extends AbstractDomainEvent
{
    /**
     * @var array
     */
    private $languages;

    /**
     * ShopLanguagesUpdated constructor.
     * @param AptoUuid $id
     * @param array $languages
     */
    public function __construct(AptoUuid $id, array $languages)
    {
        parent::__construct($id);
        $this->languages = $languages;
    }

    /**
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
}