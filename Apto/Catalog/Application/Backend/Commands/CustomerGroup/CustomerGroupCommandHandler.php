<?php

namespace Apto\Catalog\Application\Backend\Commands\CustomerGroup;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroup;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroupRepository;
use Apto\Catalog\Application\Core\Service\ShopConnector\CustomerGroupConnector;
use Apto\Catalog\Domain\Core\Model\Shop\ShopRepository;

class CustomerGroupCommandHandler implements CommandHandlerInterface
{
    /**
     * @var CustomerGroupRepository
     */
    protected $customerGroupRepository;

    /**
     * @var CustomerGroupConnector
     */
    protected $customerGroupConnector;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * @var RequestStore
     */
    protected $requestStore;

    /**
     * CustomerGroupCommandHandler constructor.
     * @param CustomerGroupRepository $customerGroupRepository
     * @param CustomerGroupConnector $customerGroupConnector
     * @param ShopRepository $shopRepository
     * @param RequestStore $requestStore
     */
    public function __construct(
        CustomerGroupRepository $customerGroupRepository,
        CustomerGroupConnector $customerGroupConnector,
        ShopRepository $shopRepository,
        RequestStore $requestStore
    ) {
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerGroupConnector = $customerGroupConnector;
        $this->shopRepository = $shopRepository;
        $this->requestStore = $requestStore;
    }

    /**
     * @param SynchronizeCustomerGroups $command
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function handleSynchronizeCustomerGroups(SynchronizeCustomerGroups $command)
    {
        // build connector configuration
        $connectorData = $this->shopRepository->findConnectorConfigByDomain($this->requestStore->getHttpHost());
        if (
            !array_key_exists('connectorUrl', $connectorData) ||
            !array_key_exists('connectorToken', $connectorData) ||
            !$connectorData['connectorUrl'] ||
            !$connectorData['connectorToken']
        ) {
            // we habe nothing to do here because no connector is configured
            return;
        }

        if ($connectorData['connectorUrl'] instanceof AptoTranslatedValue) {
            $connectorData['connectorUrl'] = $connectorData['connectorUrl']->getTranslation(
                new AptoLocale(
                    $this->requestStore->getDefaultLocale()
                )
            )->getValue();
        }

        $connectorConfig = ConnectorConfig::fromArray($connectorData);

        // get available groups from shop
        $result = $this->customerGroupConnector->findAll($connectorConfig);
        $groups = $result['result'];

        foreach ($groups as $group) {

            // create a new customer group or update existing one
            $customerGroup = $this->customerGroupRepository->findOneByShopAndExternalId($connectorConfig->getShopId(), $group['id']);
            if (null === $customerGroup) {
                // create new
                $customerGroup = new CustomerGroup(
                    $this->customerGroupRepository->nextIdentity(),
                    $group['name'],
                    $group['inputGross'],
                    $group['showGross'],
                    new AptoUuid($connectorConfig->getShopId()),
                    $group['id']
                );
                $this->customerGroupRepository->add($customerGroup);
            } else {
                // update existing
                $customerGroup
                    ->setName($group['name'])
                    ->setInputGross($group['inputGross'])
                    ->setShowGross($group['showGross']);
            }
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield SynchronizeCustomerGroups::class => [
            'method' => 'handleSynchronizeCustomerGroups',
            'bus' => 'command_bus'
        ];
    }
}