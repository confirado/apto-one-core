<?php

namespace Apto\Base\Application\Backend\Commands\AclEntry;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Backend\Model\Acl\AclEntry;
use Apto\Base\Domain\Backend\Model\Acl\AclEntryRepository;
use Apto\Base\Domain\Backend\Model\Acl\AclIdentity;
use Apto\Base\Domain\Backend\Model\Acl\AclMask;
use Apto\Base\Domain\Backend\Model\Acl\AclMaskBuilder;
use Apto\Base\Domain\Backend\Model\UserRole\UserRoleIdentifier;
use Apto\Base\Domain\Backend\Model\UserRole\UserRoleRepository;

class AclEntryHandler implements CommandHandlerInterface
{
    /**
     * @var AclEntryRepository
     */
    private $aclEntryRepository;

    /**
     * @var UserRoleRepository
     */
    private $userRoleRepository;

    /**
     * AclEntryHandler constructor.
     * @param AclEntryRepository $aclEntryRepository
     * @param UserRoleRepository $userRoleRepository
     */
    public function __construct(AclEntryRepository $aclEntryRepository, UserRoleRepository $userRoleRepository)
    {
        $this->aclEntryRepository = $aclEntryRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @param AclPermission $command
     */
    public function handleAddAclPermission(AclPermission $command)
    {
        $aclMask = $this->getAclMask($command);
        $aclIdentity = $this->getAclIdentity($command);

        $aclEntry = $this->aclEntryRepository->findByShopRoleIdentity(null, $command->getRoleId(), $aclIdentity);
        if (null !== $aclEntry) {
            if (is_array($aclEntry)) {
                $this->throwNonUniqueAclEntryException($command, $aclIdentity);
            }

            // if mask to add is not in existing mask, add it
            if (!$aclEntry->getMask()->matchedBy($aclMask)) {
                $aclMask = AclMaskBuilder::addMasks($aclEntry->getMask(), $aclMask);
                $aclEntry->setMask($aclMask);
                $this->aclEntryRepository->update($aclEntry);
            }
        } else {
            $aclEntry = new AclEntry(
                null,
                new UserRoleIdentifier($command->getRoleId()),
                $aclIdentity,
                $aclMask
            );

            $this->aclEntryRepository->add($aclEntry);
        }
    }

    /**
     * @param AclPermission $command
     */
    public function handleRemoveAclPermission(AclPermission $command)
    {
        $aclMask = $this->getAclMask($command);
        $aclIdentity = $this->getAclIdentity($command);

        $aclEntry = $this->aclEntryRepository->findByShopRoleIdentity(null, $command->getRoleId(), $aclIdentity);
        if (null !== $aclEntry) {
            if (is_array($aclEntry)) {
                $this->throwNonUniqueAclEntryException($command, $aclIdentity);
            }

            // if mask to remove is in existing mask, subtract it
            if ($aclEntry->getMask()->matchedBy($aclMask)) {
                $aclMask = AclMaskBuilder::subtractMasks($aclEntry->getMask(), $aclMask);
                $aclEntry->setMask($aclMask);

                if (!$aclEntry->getMask()->isNone()) {
                    $this->aclEntryRepository->update($aclEntry);
                }
            }

            if ($aclEntry->getMask()->isNone()) {
                $this->aclEntryRepository->remove($aclEntry);
            }
        }
    }

    /**
     * @param AclPermission $command
     * @return AclIdentity
     */
    private function getAclIdentity(AclPermission $command): AclIdentity
    {
        return new AclIdentity($command->getEntityClass(), $command->getEntityId(), $command->getEntityField());
    }

    /**
     * @param AclPermission $command
     * @return AclMask
     */
    private function getAclMask(AclPermission $command): AclMask
    {
        $aclMaskBuilder = new AclMaskBuilder();
        $aclMaskBuilder->add($command->getPermissions());
        return $aclMaskBuilder->get();
    }

    /**
     * @param AclPermission $command
     * @param AclIdentity $aclIdentity
     * @throws NonUniqueAclEntryException
     */
    private function throwNonUniqueAclEntryException(AclPermission $command, AclIdentity $aclIdentity)
    {
        throw new NonUniqueAclEntryException('An AclEntry with the following properties already exists: shopId('.$command->getShopId().'), userRoleId('.$command->getRoleId().'), entityClass('.$aclIdentity->getModelClass().'), entityId('.$aclIdentity->getEntityId().'), entityFieldName('.$aclIdentity->getFieldName().').');
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddAclPermission::class => [
            'method' => 'handleAddAclPermission',
            'bus' => 'command_bus'
        ];

        yield RemoveAclPermission::class => [
            'method' => 'handleRemoveAclPermission',
            'bus' => 'command_bus'
        ];
    }
}