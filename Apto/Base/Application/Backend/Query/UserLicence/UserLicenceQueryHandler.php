<?php

namespace Apto\Base\Application\Backend\Query\UserLicence;

use Apto\Base\Application\Core\QueryHandlerInterface;

class UserLicenceQueryHandler implements QueryHandlerInterface
{
    /**
     * @var UserLicenceFinder
     */
    private $userLicenceFinder;

    /**
     * UserQueryHandler constructor.
     * @param UserLicenceFinder $userLicenceFinder
     */
    public function __construct(UserLicenceFinder $userLicenceFinder)
    {
        $this->userLicenceFinder = $userLicenceFinder;
    }

    /**
     * @param FindUserLicence $query
     * @return array
     */
    public function handleFindUserLicence(FindUserLicence $query)
    {
        return $this->userLicenceFinder->findById($query->getId());
    }

    /**
     * @param FindCurrentUserLicence $query
     * @return array
     */
    public function handleFindCurrentUserLicence(FindCurrentUserLicence $query)
    {
        return $this->userLicenceFinder->findCurrent();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindUserLicence::class => [
            'method' => 'handleFindUserLicence',
            'bus' => 'query_bus'
        ];

        yield FindCurrentUserLicence::class => [
            'method' => 'handleFindCurrentUserLicence',
            'bus' => 'query_bus'
        ];
    }
}