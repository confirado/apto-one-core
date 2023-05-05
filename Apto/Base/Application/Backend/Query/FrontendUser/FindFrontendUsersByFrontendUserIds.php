<?php

namespace Apto\Base\Application\Backend\Query\FrontendUser;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindFrontendUsersByFrontendUserIds implements PublicQueryInterface
{
    /**
     * @var array
     */
    private $userIds;

    /**
     * @var bool
     */
    private $secure;

    /**
     * FindUsersByUserIds constructor.
     * @param array $userIds
     * @param bool $secure
     */
    public function __construct(array $userIds = [], bool $secure = true)
    {
        $this->userIds = $userIds;
        $this->secure = $secure;
    }

    /**
     * @return array
     */
    public function getUserIds(): array
    {
        return $this->userIds;
    }

    /**
     * @return bool
     */
    public function getSecure(): bool
    {
        return $this->secure;
    }
}
