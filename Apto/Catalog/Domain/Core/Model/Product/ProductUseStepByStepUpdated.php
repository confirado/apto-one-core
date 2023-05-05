<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductUseStepByStepUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $useStepByStep;

    /**
     * ProductUseStepByStepUpdated constructor.
     * @param AptoUuid $id
     * @param bool $useStepByStep
     */
    public function __construct(AptoUuid $id, bool $useStepByStep)
    {
        parent::__construct($id);
        $this->useStepByStep = $useStepByStep;
    }

    /**
     * @return bool
     */
    public function getUseStepByStep(): bool
    {
        return $this->useStepByStep;
    }
}