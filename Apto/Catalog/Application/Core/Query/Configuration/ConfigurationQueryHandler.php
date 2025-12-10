<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Catalog\Application\Core\Query\Product\Element\ProductElementFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;

class ConfigurationQueryHandler implements QueryHandlerInterface
{
    /**
     * @var AptoJsonSerializer
     */
    protected $aptoJsonSerializer;

    /**
     * @var BasketConfigurationFinder
     */
    protected $basketConfigurationFinder;

    /**
     * @var CustomerConfigurationFinder
     */
    protected $customerConfigurationFinder;

    /**
     * @var OrderConfigurationFinder
     */
    protected $orderConfigurationFinder;

    /**
     * @var ProposedConfigurationFinder
     */
    protected $proposedConfigurationFinder;

    /**
     * @var SharedConfigurationFinder
     */
    protected $sharedConfigurationFinder;

    /**
     * @var GuestConfigurationFinder
     */
    protected $guestConfigurationFinder;

    /**
     * @var ImmutableConfigurationFinder
     */
    protected $immutableConfigurationFinder;

    /**
     * @var CodeConfigurationFinder
     */
    protected $codeConfigurationFinder;

    /**
     * @var ProductElementFinder
     */
    private $productElementFinder;

    /**
     * @param AptoJsonSerializer $aptoJsonSerializer
     * @param BasketConfigurationFinder $basketConfigurationFinder
     * @param CustomerConfigurationFinder $customerConfigurationFinder
     * @param OrderConfigurationFinder $orderConfigurationFinder
     * @param ProposedConfigurationFinder $proposedConfigurationFinder
     * @param SharedConfigurationFinder $sharedConfigurationFinder
     * @param GuestConfigurationFinder $guestConfigurationFinder
     * @param ImmutableConfigurationFinder $immutableConfigurationFinder
     * @param CodeConfigurationFinder $codeConfigurationFinder
     * @param ProductElementFinder $productElementFinder
     */
    public function __construct (
        AptoJsonSerializer $aptoJsonSerializer,
        BasketConfigurationFinder $basketConfigurationFinder,
        CustomerConfigurationFinder $customerConfigurationFinder,
        OrderConfigurationFinder $orderConfigurationFinder,
        ProposedConfigurationFinder $proposedConfigurationFinder,
        SharedConfigurationFinder $sharedConfigurationFinder,
        GuestConfigurationFinder $guestConfigurationFinder,
        ImmutableConfigurationFinder $immutableConfigurationFinder,
        CodeConfigurationFinder $codeConfigurationFinder,
        ProductElementFinder $productElementFinder
    ) {
        $this->aptoJsonSerializer = $aptoJsonSerializer;
        $this->basketConfigurationFinder = $basketConfigurationFinder;
        $this->customerConfigurationFinder = $customerConfigurationFinder;
        $this->orderConfigurationFinder = $orderConfigurationFinder;
        $this->proposedConfigurationFinder = $proposedConfigurationFinder;
        $this->sharedConfigurationFinder = $sharedConfigurationFinder;
        $this->guestConfigurationFinder = $guestConfigurationFinder;
        $this->immutableConfigurationFinder = $immutableConfigurationFinder;
        $this->codeConfigurationFinder = $codeConfigurationFinder;
        $this->productElementFinder = $productElementFinder;
    }

    /**
     * @param FindBasketConfiguration $query
     * @return array|null
     * @throws AptoJsonSerializerException
     */
    public function handleFindBasketConfiguration(FindBasketConfiguration $query)
    {
        $result = $this->basketConfigurationFinder->findById($query->getConfigurationId());

        if (null === $result) {
            return $result;
        }

        return $this->prepareResult($result, 'basket');
    }

    /**
     * @param FindCustomerConfiguration $query
     * @return array|null
     * @throws AptoJsonSerializerException
     */
    public function handleFindCustomerConfiguration(FindCustomerConfiguration $query)
    {
        $result = $this->customerConfigurationFinder->findById($query->getConfigurationId());

        if (null === $result) {
            return $result;
        }

        return $this->prepareResult($result, 'customer');
    }

    /**
     * @param FindOrderConfiguration $query
     * @return array|null
     * @throws AptoJsonSerializerException
     */
    public function handleFindOrderConfiguration(FindOrderConfiguration $query)
    {
        $result = $this->orderConfigurationFinder->findById($query->getConfigurationId());

        if (null === $result) {
            return $result;
        }

        return $this->prepareResult($result, 'order');
    }

    /**
     * @param FindProposedConfiguration $query
     * @return array|null
     * @throws AptoJsonSerializerException
     */
    public function handleFindProposedConfiguration(FindProposedConfiguration $query)
    {
        $result = $this->proposedConfigurationFinder->findById($query->getConfigurationId());

        if (null === $result) {
            return $result;
        }

        return $this->prepareResult($result, 'proposed');
    }

    /**
     * @param FindSharedConfiguration $query
     * @return array|null
     * @throws AptoJsonSerializerException
     */
    public function handleFindSharedConfiguration(FindSharedConfiguration $query)
    {
        $result = $this->sharedConfigurationFinder->findById($query->getConfigurationId());

        if (null === $result) {
            return $result;
        }

        return $this->prepareResult($result, 'shared');
    }

    /**
     * @param FindGuestConfiguration $query
     * @return array|null
     * @throws AptoJsonSerializerException
     */
    public function handleFindGuestConfiguration(FindGuestConfiguration $query)
    {
        $result = $this->guestConfigurationFinder->findById($query->getConfigurationId());

        if (null === $result) {
            return $result;
        }

        return $this->prepareResult($result, 'guest');
    }

    /**
     * @param FindImmutableConfiguration $query
     * @return array|null
     * @throws AptoJsonSerializerException
     */
    public function handleFindImmutableConfiguration(FindImmutableConfiguration $query)
    {
        $result = $this->immutableConfigurationFinder->findById($query->getConfigurationId());

        if (null === $result) {
            return $result;
        }

        return $this->prepareResult($result, 'immutable');
    }

    /**
     * @param FindCodeConfiguration $query
     * @return array|null
     * @throws AptoJsonSerializerException
     */
    public function handleFindCodeConfiguration(FindCodeConfiguration $query)
    {
        // find by code
        $result = $this->codeConfigurationFinder->findByCode($query->getConfigurationId());
        if (null !== $result) {
            return $this->prepareResult($result, 'code');
        }

        // find by id
        $result = $this->codeConfigurationFinder->findById($query->getConfigurationId());
        if (null !== $result) {
            return $this->prepareResult($result, 'code');
        }

        return $result;
    }

    /**
     * @param FindBasketConfigurationsByIdList $query
     * @return array
     */
    public function handleFindBasketConfigurationsByIdList(FindBasketConfigurationsByIdList $query)
    {
        return $this->basketConfigurationFinder->findBasketConfigurationByIdList($query->getIdList());
    }

    /**
     * @param FindCustomerConfigurations $query
     * @return array
     */
    public function handleFindCustomerConfigurations(FindCustomerConfigurations $query)
    {
        return $this->customerConfigurationFinder->findConfigurations($query->getSearchString());
    }

    /**
     * @param FindProposedConfigurations $query
     * @return array
     * @throws AptoJsonSerializerException
     */
    public function handleFindProposedConfigurations(FindProposedConfigurations $query)
    {
        $perspective = 'persp1';
        $result = $this->proposedConfigurationFinder->findConfigurations($query->getProductId(), $query->getSearchString());

        foreach ($result['data'] as &$configuration) {
            $configuration = $this->prepareResult($configuration, 'proposed');

            $state = new State($configuration['state']);
            $imageList = $this->productElementFinder->findRenderImagesByState($state, $perspective);
        }

        return $result;
    }

    /**
     * @param array $result
     * @param string $type
     * @return array
     * @throws AptoJsonSerializerException
     */
    protected function prepareResult(array $result, string $type): array
    {
        $result['state'] = $this->decodeState($result['state']);
        $result['type'] = $type;

        if(isset($result['product'][0]['id'])) {
            $result['productId'] = $result['product'][0]['id'];
            unset($result['product']);
        }

        return $result;
    }

    /**
     * @param string $encodedState
     * @return array
     * @throws AptoJsonSerializerException
     */
    protected function decodeState(string $encodedState): array
    {
        /** @var State $state */
        $state = $this->aptoJsonSerializer->jsonUnSerialize($encodedState);
        return $state->jsonSerialize();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindBasketConfiguration::class => [
            'method' => 'handleFindBasketConfiguration',
            'bus' => 'query_bus'
        ];

        yield FindCustomerConfiguration::class => [
            'method' => 'handleFindCustomerConfiguration',
            'bus' => 'query_bus'
        ];

        yield FindOrderConfiguration::class => [
            'method' => 'handleFindOrderConfiguration',
            'bus' => 'query_bus'
        ];

        yield FindProposedConfiguration::class => [
            'method' => 'handleFindProposedConfiguration',
            'bus' => 'query_bus'
        ];

        yield FindSharedConfiguration::class => [
            'method' => 'handleFindSharedConfiguration',
            'bus' => 'query_bus'
        ];

        yield FindGuestConfiguration::class => [
            'method' => 'handleFindGuestConfiguration',
            'bus' => 'query_bus'
        ];

        yield FindImmutableConfiguration::class => [
            'method' => 'handleFindImmutableConfiguration',
            'bus' => 'query_bus'
        ];

        yield FindCodeConfiguration::class => [
            'method' => 'handleFindCodeConfiguration',
            'bus' => 'query_bus'
        ];

        yield FindBasketConfigurationsByIdList::class => [
            'method' => 'handleFindBasketConfigurationsByIdList',
            'bus' => 'query_bus'
        ];

        yield FindCustomerConfigurations::class => [
            'method' => 'handleFindCustomerConfigurations',
            'bus' => 'query_bus'
        ];

        yield FindProposedConfigurations::class => [
            'method' => 'handleFindProposedConfigurations',
            'bus' => 'query_bus'
        ];
    }
}
