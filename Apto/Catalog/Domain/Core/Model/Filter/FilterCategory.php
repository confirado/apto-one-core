<?php
namespace Apto\Catalog\Domain\Core\Model\Filter;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;

class FilterCategory extends AptoAggregate
{
    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var Identifier
     */
    protected $identifier;

    /**
     * @var int
     */
    protected $position;

    /**
     * Category constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     * @param Identifier $identifier
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name, Identifier $identifier)
    {
        parent::__construct($id);
        $this->publish(
            new FilterCategoryAdded(
                $this->getId()
            )
        );
        $this->setName($name);
        $this->identifier = $identifier;
        $this->position = 0;
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
     * @return FilterCategory
     */
    public function setName(AptoTranslatedValue $name)
    {
        if (null !== $this->name && $this->name->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new FilterCategoryNameUpdated(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @return Identifier
     */
    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @param Identifier $identifier
     * @return FilterCategory
     */
    public function setIdentifier(Identifier $identifier)
    {
        if ($this->identifier->equals($identifier)) {
            return $this;
        }

        $this->identifier = $identifier;
        $this->publish(
            new FilterCategoryIdentifierUpdated(
                $this->getId(),
                $this->identifier->getValue()
            )
        );
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return FilterCategory
     */
    public function setPosition(int $position = 0)
    {
        if ($this->position === $position) {
            return $this;
        }
        $this->position = $position;
        $this->publish(
            new FilterCategoryPositionUpdated(
                $this->getId(),
                $this->getPosition()
            )
        );
        return $this;
    }

}