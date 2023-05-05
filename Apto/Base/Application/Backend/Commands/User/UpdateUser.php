<?php

namespace Apto\Base\Application\Backend\Commands\User;

class UpdateUser extends AbstractAddUser
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateUser constructor.
     * @param string $id
     * @param bool $active
     * @param string $username
     * @param $plainPassword
     * @param string $email
     * @param array $userRoles
     * @param string|null $rte
     * @param string|null $apiKey
     * @param string|null $apiOrigin
     */
    public function __construct(string $id, bool $active, string $username, $plainPassword, string $email, array $userRoles, string $rte = null, string $apiKey = null, string $apiOrigin = null)
    {
        parent::__construct($active, $username, $plainPassword, $email, $userRoles, $rte, $apiKey, $apiOrigin);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}