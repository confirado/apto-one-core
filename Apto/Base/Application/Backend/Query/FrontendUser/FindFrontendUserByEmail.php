<?php

namespace Apto\Base\Application\Backend\Query\FrontendUser;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindFrontendUserByEmail implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $secure;

    /**
     * FindFrontendUserByEmail constructor.
     * @param string $email
     * @param bool $secure
     */
    public function __construct(string $email, bool $secure = true)
    {
        $this->email = $email;
        $this->secure = $secure;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function getSecure(): bool
    {
        return $this->secure;
    }
}
