<?php

namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceMatrixNameUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * PriceMatrixAdded constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->name = $name;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }
}