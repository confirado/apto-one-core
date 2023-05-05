<?php

namespace Apto\Base\Application\Backend\Query\FrontendUser;

use Apto\Base\Application\Core\QueryHandlerInterface;

class FrontendUserQueryHandler implements QueryHandlerInterface
{
    /**
     * @var FrontendUserFinder
     */
    private $frontendUserFinder;

    /**
     * UserQueryHandler constructor.
     * @param FrontendUserFinder $frontendUserFinder
     */
    public function __construct(FrontendUserFinder $frontendUserFinder)
    {
        $this->frontendUserFinder = $frontendUserFinder;
    }

    /**
     * @param FindFrontendUser $query
     * @return array
     */
    public function handleFindFrontendUser(FindFrontendUser $query)
    {
        return $this->frontendUserFinder->findById($query->getId(), $query->getSecure());
    }

    /**
     * @param FindFrontendUserByUsername $query
     * @return array
     */
    public function handleFindFrontendUserByUsername(FindFrontendUserByUsername $query)
    {
        return $this->frontendUserFinder->findByUsername($query->getUsername(), $query->getSecure());
    }

    /**
     * @param FindFrontendUserByEmail $query
     * @return array|null
     */
    public function handleFindFrontendUserByEmail(FindFrontendUserByEmail $query)
    {
        return $this->frontendUserFinder->findByEmail($query->getEmail(), $query->getSecure());
    }

    /**
     * @param FindCurrentFrontendUser $query
     * @return array|null
     */
    public function handleFindCurrentFrontendUser(FindCurrentFrontendUser $query)
    {
        return $this->frontendUserFinder->findByUsername($query->getUsername());
    }

    /**
     * @param FindFrontendUsers $query
     * @return array
     */
    public function handleFindFrontendUsers(FindFrontendUsers $query)
    {
        return $this->frontendUserFinder->findUsers($query->getSearchString(), $query->getSecure());
    }

    /**
     * @param FindFrontendUsersByFrontendUserIds $query
     * @return mixed
     */
    public function handleFindFrontendUsersByFrontendUserIds(FindFrontendUsersByFrontendUserIds $query)
    {
        return $this->frontendUserFinder->findFindUsersByUserIds($query->getUserIds(), $query->getSecure());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindCurrentFrontendUser::class => [
            'method' => 'handleFindCurrentFrontendUser',
            'bus' => 'query_bus'
        ];

        yield FindFrontendUser::class => [
            'method' => 'handleFindFrontendUser',
            'bus' => 'query_bus'
        ];

        yield FindFrontendUserByEmail::class => [
            'method' => 'handleFindFrontendUserByEmail',
            'bus' => 'query_bus'
        ];

        yield FindFrontendUserByUsername::class => [
            'method' => 'handleFindFrontendUserByUsername',
            'bus' => 'query_bus'
        ];

        yield FindFrontendUsers::class => [
            'method' => 'handleFindFrontendUsers',
            'bus' => 'query_bus'
        ];

        yield FindFrontendUsersByFrontendUserIds::class => [
            'method' => 'handleFindFrontendUsersByFrontendUserIds',
            'bus' => 'query_bus'
        ];
    }
}
