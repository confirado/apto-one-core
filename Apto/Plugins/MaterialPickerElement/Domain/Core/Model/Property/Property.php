<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use phpDocumentor\Reflection\Types\Boolean;

class Property extends AptoAggregate
{
    use AptoCustomProperties;

    /**
     * @var Group
     */
    private $group;

    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * @var bool
     */
    private $isDefault;

    /**
     * Property constructor.
     * @param Group $group
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, Group $group, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->group = $group;
        $this->name = $name;
        $this->isDefault = false;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @param AptoTranslatedValue $name
     * @return Property
     */
    public function setName(AptoTranslatedValue $name): Property
    {
        if (!$this->name->equals($name)) {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     * @return Property
     */
    public function setIsDefault(bool $isDefault): Property
    {
        $this->isDefault = $isDefault;
        return $this;
    }

}