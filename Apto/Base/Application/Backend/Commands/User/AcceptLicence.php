<?php

namespace Apto\Base\Application\Backend\Commands\User;

use Apto\Base\Application\Core\CommandInterface;

class AcceptLicence implements CommandInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $userLicenceId;

    /**
     * AcceptLicence constructor.
     * @param string $username
     * @param string $userLicenceId
     */
    public function __construct(string $username, string $userLicenceId)
    {
        $this->username = $username;
        $this->userLicenceId = $userLicenceId;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getUserLicenceId(): string
    {
        return $this->userLicenceId;
    }
}