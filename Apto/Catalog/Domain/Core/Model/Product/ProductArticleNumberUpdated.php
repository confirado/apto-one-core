<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductArticleNumberUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $articleNumber;

    /**
     * ProductArticleNumberUpdated constructor.
     * @param AptoUuid $id
     * @param string $articleNumber
     */
    public function __construct(AptoUuid $id, string $articleNumber)
    {
        parent::__construct($id);
        $this->articleNumber = $articleNumber;
    }

    /**
     * @return string
     */
    public function getArticleNumber(): string
    {
        return $this->articleNumber;
    }
}
