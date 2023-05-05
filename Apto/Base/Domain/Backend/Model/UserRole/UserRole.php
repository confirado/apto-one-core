<?php

namespace Apto\Base\Domain\Backend\Model\UserRole;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class UserRole extends AptoAggregate
{
    /**
     * @var UserRoleIdentifier
     */
    protected $identifier;

    /**
     * @var Collection;
     */
    protected $parents;

    /**
     * @var Collection
     */
    protected $children;

    /**
     * @var string
     */
    protected $name;

    /**
     * UserRole constructor.
     * @param AptoUuid $id
     * @param UserRoleIdentifier $identifier
     * @param string $name
     */
    public function __construct(AptoUuid $id, UserRoleIdentifier $identifier, $name)
    {
        parent::__construct($id);
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this
            ->setIdentifier($identifier)
            ->setName($name);
    }

    /**
     * @return UserRoleIdentifier
     */
    public function getIdentifier(): UserRoleIdentifier
    {
        return $this->identifier;
    }

    /**
     * @param UserRoleIdentifier $identifier
     * @return UserRole
     */
    public function setIdentifier(UserRoleIdentifier $identifier): UserRole
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    /**
     * @return Collection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Collection $children
     * @return UserRole
     */
    public function setChildren(Collection $children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return UserRole
     */
    public function setName(string $name): UserRole
    {
        $this->name = $name;
        return $this;
    }
}