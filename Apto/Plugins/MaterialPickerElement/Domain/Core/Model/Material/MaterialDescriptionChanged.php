<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialDescriptionChanged extends AbstractDomainEvent
{
    /**
     * @var AptoTranslatedValue
     */
    private $description;

    /**
     * MaterialDescriptionChanged constructor.
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