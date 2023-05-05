<?php

namespace Apto\Base\Application\Backend\Query\AclEntry;

use Apto\Base\Application\Core\QueryHandlerInterface;

class AclEntryQueryHandler implements QueryHandlerInterface
{
    /**
     * @var AclEntryFinder
     */
    private $aclEntryFinder;

    /**
     * AclEntryQueryHandler constructor.
     * @param AclEntryFinder $aclEntryFinder
     */
    public function __construct(AclEntryFinder $aclEntryFinder)
    {
        $this->aclEntryFinder = $aclEntryFinder;
    }

    /**
     * @param FindAclEntriesByAclClass $query
     * @return mixed
     */
    public function handleFindAclEntriesByAclClass(FindAclEntriesByAclClass $query)
    {
        return $this->aclEntryFinder->findByAclClass($query->getAclClass());
    }

    /**
     * @param FindByShopRoleIdentity $query
     * @return array
     */
    public function handleFindByShopRoleIdentity(FindByShopRoleIdentity $query)
    {
        return $this->aclEntryFinder->findByShopRoleIdentity(
            $query->getRoleIdentifier(),
            $query->getModelClass(),
            $query->getShopId(),
            $query->getEntityId(),
            $query->getFieldName()
        );
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindAclEntriesByAclClass::class => [
            'method' => 'handleFindAclEntriesByAclClass',
            'bus' => 'query_bus'
        ];

        yield FindByShopRoleIdentity::class => [
            'method' => 'handleFindByShopRoleIdentity',
            'bus' => 'query_bus'
        ];
    }
}