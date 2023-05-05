<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialAbsorptionChanged extends AbstractDomainEvent
{
    /**
     * @var int|null
     */
    private $absorption;

    /**
     * MaterialAbsorptionChanged constructor.
     * @param AptoUuid $id
     * @param int|null $absorption
     */
    public function __construct(AptoUuid $id, $absorption)
    {
        parent::__construct($id);
        $this->absorption = $absorption;
    }

    /**
     * @return int|null
     */
    public function getAbsorption()
    {
        return $this->absorption;
    }
}