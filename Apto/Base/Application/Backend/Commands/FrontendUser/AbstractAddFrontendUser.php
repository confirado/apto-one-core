<?php

namespace Apto\Base\Application\Backend\Commands\FrontendUser;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddFrontendUser implements CommandInterface
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
     * @var string
     */
    private $externalCustomerGroupId;

    /**
     * AddFrontendUser constructor.
     * @param bool $active
     * @param string $username
     * @param $plainPassword
     * @param string $email
     * @param string $externalCustomerGroupId
     */
    public function __construct(bool $active, string $username, $plainPassword, string $email, string $externalCustomerGroupId)
    {
        $this->active = $active;
        $this->username = strtolower($username);
        $this->plainPassword = $plainPassword;
        $this->email = strtolower($email);
        $this->externalCustomerGroupId = $externalCustomerGroupId;
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
     * @return string
     */
    public function getExternalCustomerGroupId(): string
    {
        return $this->externalCustomerGroupId;
    }
}
