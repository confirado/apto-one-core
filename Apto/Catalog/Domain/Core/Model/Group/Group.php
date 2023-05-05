<?php

namespace Apto\Catalog\Domain\Core\Model\Group;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\IdentifierNullable;

class Group extends AptoAggregate
{
    /**
     * @var AptoTranslatedValue
     */
    private $name;

    /**
     * @var int
     */
    private $position;

    /**
     * @var IdentifierNullable
     */
    protected $identifier;

    /**
     * Group constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->position = 0;
        $this->publish(
            new GroupAdded(
                $this->getId()
            )
        );
        $this->setName($name);
        $this->identifier = new IdentifierNullable();
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
     * @return Group
     */
    public function setName(AptoTranslatedValue $name): Group
    {
        if (null !== $this->name && $this->name->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new GroupNameUpdated(
                $this->getId(),
                $this->getName()
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
     * @return Group
     */
    public function setPosition(int $position): Group
    {
        if ($this->getPosition() === $position) {
            return $this;
        }

        $this->position = $position;
        $this->publish(
            new GroupPositionUpdated(
                $this->getId(),
                $this->getPosition()
            )
        );
        return $this;
    }

    /**
     * @return IdentifierNullable
     */
    public function getIdentifier(): IdentifierNullable
    {
        return $this->identifier;
    }

    /**
     * @param IdentifierNullable $identifier
     * @return Group
     */
    public function setIdentifier(IdentifierNullable $identifier): Group
    {
        if ($this->identifier->equals($identifier)) {
            return $this;
        }

        $this->identifier = $identifier;
        $this->publish(
            new GroupIdentifierUpdated(
                $this->getId(),
                $this->identifier->getValue()
            )
        );
        return $this;
    }
}