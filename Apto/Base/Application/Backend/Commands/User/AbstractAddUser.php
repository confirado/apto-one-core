<?php

namespace Apto\Base\Application\Backend\Commands\User;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddUser implements CommandInterface
{
    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string|null
     */
    private $plainPassword;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $userRoles;

    /**
     * @var string
     */
    private $rte;

    /**
     * @var string|null
     */
    private $apiKey;

    /**
     * @var string|null
     */
    private $apiOrigin;

    /**
     * AddUser constructor.
     * @param bool $active
     * @param string $username
     * @param $plainPassword
     * @param string $email
     * @param array $userRoles
     * @param string|null $rte
     * @param string|null $apiKey
     * @param string|null $apiOrigin
     */
    public function __construct(bool $active, string $username, $plainPassword, string $email, array $userRoles, string $rte = null, string $apiKey = null, string $apiOrigin = null)
    {
        $this->active = $active;
        $this->username = strtolower($username);
        $this->plainPassword = $plainPassword;
        $this->email = strtolower($email);
        $this->userRoles = $userRoles;
        $this->rte = null === $rte ? '' : $rte;
        $this->apiKey = $apiKey;
        $this->apiOrigin = $apiOrigin;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getUserRoles(): array
    {
        return $this->userRoles;
    }

    /**
     * @return string
     */
    public function getRte(): string
    {
        return $this->rte;
    }

    /**
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string|null
     */
    public function getApiOrigin(): ?string
    {
        return $this->apiOrigin;
    }
}