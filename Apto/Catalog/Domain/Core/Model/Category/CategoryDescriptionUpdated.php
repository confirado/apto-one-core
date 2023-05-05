<?php

namespace Apto\Catalog\Domain\Core\Model\Category;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class CategoryDescriptionUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue
     */
    private $description;

    /**
     * CategoryDescriptionUpdated constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $description
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $description)
    {
        parent::__construct($id);
        $this->description = $description;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getDescription(): AptoTranslatedValue
    {
        return $this->description;
    }
}