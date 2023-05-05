<?php

namespace Apto\Base\Domain\Backend\Model\UserRole;

class UserRoleIdentifier
{

    /**
     * @var string
     */
    protected $identifier;

    /**
     * UserRoleIdentifier constructor.
     * @param string $identifier
     * @throws InvalidUserRoleIdentifierException
     */
    public function __construct(string $identifier)
    {
        $this->setIdentifier($identifier);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return UserRoleIdentifier
     * @throws InvalidUserRoleIdentifierException
     */
    protected function setIdentifier(string $identifier): UserRoleIdentifier
    {
        $identifier = strtoupper($identifier);
        if ('ROLE_' != substr($identifier, 0, 5)) {
            throw new InvalidUserRoleIdentifierException('Invalid value for UserRoleIdentifier. Identifier must start with "ROLE_"');
        }
        if ('ROLE_SUPER_ADMIN' == $identifier) {
            throw new InvalidUserRoleIdentifierException('Invalid value for UserRoleIdentifier. Identifier cant use reserved keyword "ROLE_SUPER_ADMIN"');
        }

        $this->identifier = $identifier;

        return $this;
    }

}