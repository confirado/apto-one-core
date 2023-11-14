<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Application\Core\EventBusInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Customer\Customer;
use Apto\Base\Domain\Core\Model\Customer\CustomerRepository;
use Apto\Base\Domain\Core\Model\Customer\CustomerUserName;
use Apto\Base\Domain\Core\Model\Customer\Gender;
use Apto\Base\Domain\Core\Model\Email;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Catalog\Application\Core\Service\ConfigurableProduct\ConfigurableProductBuilder;
use Apto\Catalog\Application\Core\Service\ShopConnector\BasketConnector;
use Apto\Catalog\Application\Core\Service\TaxCalculator\SimpleTaxCalculator;
use Apto\Catalog\Application\Frontend\Events\Configuration\ConfigurationFinished;
use Apto\Catalog\Application\Frontend\Events\Configuration\GuestConfigurationAdded;
use Apto\Catalog\Application\Frontend\Events\Configuration\OfferConfigurationAdded;
use Apto\Catalog\Application\Frontend\Service\BasketItemFactory;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProductFactory;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfigurationRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\CustomerConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\CustomerConfigurationRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\GuestConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\GuestConfigurationRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\ImmutableConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\ImmutableConfigurationRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\CodeConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\CodeConfigurationRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\OrderConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\OrderConfigurationRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\ProposedConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\ProposedConfigurationRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Catalog\Domain\Core\Model\Shop\ShopRepository;

use Exception;
use Apto\Base\Domain\Core\Model\EmailValidationException;
use Apto\Base\Domain\Core\Model\Customer\InvalidGenderException;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Configuration\ConfigurationInvalidMultipleSelection;
use Apto\Catalog\Domain\Core\Model\Configuration\ConfigurationProductRulesNotFulfilled;
use Apto\Catalog\Domain\Core\Model\Configuration\ConfigurationValueNotAllowedException;
use Apto\Catalog\Domain\Core\Service\EnrichedStateValidation\RuleValidationService;
use Apto\Catalog\Domain\Core\Service\StateValidation\ValueValidationService;

class ConfigurationCommandHandler extends AbstractCommandHandler
{
    /**
     * @var RequestStore
     */
    protected $requestSessionStore;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var BasketConfigurationRepository
     */
    protected $basketConfigurationRepository;

    /**
     * @var CustomerConfigurationRepository
     */
    protected $customerConfigurationRepository;

    /**
     * @var OrderConfigurationRepository
     */
    protected $orderConfigurationRepository;

    /**
     * @var ProposedConfigurationRepository
     */
    protected $proposedConfigurationRepository;

    /**
     * @var GuestConfigurationRepository
     */
    protected $guestConfigurationRepository;

    /**
     * @var CodeConfigurationRepository
     */
    protected $codeConfigurationRepository;

    /**
     * @var ImmutableConfigurationRepository
     */
    protected $immutableConfigurationRepository;

    /**
     * @var BasketConnector
     */
    protected $basketConnector;

    /**
     * @var BasketItemFactory
     */
    protected $basketItemFactory;

    /**
     * @var ConfigurableProductFactory
     */
    protected $configurableProductFactory;

    /**
     * @var ValueValidationService
     */
    protected $valueValidationService;

    /**
     * @var RuleValidationService
     */
    protected $ruleValidationService;

    /**
     * @var EventBusInterface
     */
    protected $eventBus;

    /**
     * @param RequestStore $requestSessionStore
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @param CustomerRepository $customerRepository
     *
     * @param BasketConfigurationRepository $basketConfigurationRepository
     * @param CustomerConfigurationRepository $customerConfigurationRepository
     * @param OrderConfigurationRepository $orderConfigurationRepository
     * @param ProposedConfigurationRepository $proposedConfigurationRepository
     * @param GuestConfigurationRepository $guestConfigurationRepository
     * @param ImmutableConfigurationRepository $immutableConfigurationRepository
     * @param CodeConfigurationRepository $codeConfigurationRepository
     *
     * @param BasketConnector $basketConnector
     * @param BasketItemFactory $basketItemFactory
     * @param ConfigurableProductBuilder $configurableProductBuilder
     * @param EventBusInterface $eventBus
     */
    public function __construct(
        RequestStore $requestSessionStore,
        ShopRepository $shopRepository,
        ProductRepository $productRepository,
        CustomerRepository $customerRepository,

        BasketConfigurationRepository $basketConfigurationRepository,
        CustomerConfigurationRepository $customerConfigurationRepository,
        OrderConfigurationRepository $orderConfigurationRepository,
        ProposedConfigurationRepository $proposedConfigurationRepository,
        GuestConfigurationRepository $guestConfigurationRepository,
        ImmutableConfigurationRepository $immutableConfigurationRepository,
        CodeConfigurationRepository $codeConfigurationRepository,

        BasketConnector $basketConnector,
        BasketItemFactory $basketItemFactory,
        ConfigurableProductBuilder $configurableProductBuilder,
        RuleValidationService $ruleValidationService,
        EventBusInterface $eventBus
    ) {
        $this->requestSessionStore = $requestSessionStore;
        $this->shopRepository = $shopRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;

        $this->basketConfigurationRepository = $basketConfigurationRepository;
        $this->customerConfigurationRepository = $customerConfigurationRepository;
        $this->orderConfigurationRepository = $orderConfigurationRepository;
        $this->proposedConfigurationRepository = $proposedConfigurationRepository;
        $this->guestConfigurationRepository = $guestConfigurationRepository;
        $this->immutableConfigurationRepository = $immutableConfigurationRepository;
        $this->codeConfigurationRepository = $codeConfigurationRepository;

        $this->basketConnector = $basketConnector;
        $this->basketItemFactory = $basketItemFactory;
        $this->configurableProductFactory = new ConfigurableProductFactory($configurableProductBuilder, $productRepository);
        $this->valueValidationService = new ValueValidationService();
        $this->ruleValidationService = $ruleValidationService;

        $this->eventBus = $eventBus;
    }

    /**
     * @param AddBasketConfiguration $command
     * @return void
     * @throws AptoJsonSerializerException
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws InvalidUuidException
     */
    public function handleAddBasketConfiguration(AddBasketConfiguration $command)
    {
        // get product from repository
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        // assign required variables
        $locale = new AptoLocale($command->getLocale());
        $connectorConfig = $this->getConnectorConfig($command->getSessionCookies(), $locale);
        $state = new State($command->getState());

        // assert valid values and rules
        $configurableProduct = $this->configurableProductFactory->fromProductId($product->getId());
        $this->valueValidationService->assertValidValues($configurableProduct, $state);
        $this->ruleValidationService->validateState($configurableProduct, $state);

        // create new basket configuration
        $basketConfiguration = new BasketConfiguration(
            $this->basketConfigurationRepository->nextIdentity(),
            $product,
            $state
        );
        $this->basketConfigurationRepository->add($basketConfiguration);
        $basketConfiguration->publishEvents();

        // push basket item
        $this->pushBasketItem(
            $basketConfiguration,
            $connectorConfig,
            $product,
            $locale,
            $command->getQuantity(),
            $command->getPerspectives(),
            $command->getAdditionalData()
        );
    }

    /**
     * @param UpdateBasketConfiguration $command
     * @return void
     * @throws AptoJsonSerializerException
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws InvalidUuidException
     */
    public function handleUpdateBasketConfiguration(UpdateBasketConfiguration $command)
    {
        // get product from repository
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        // get basket configuration from repository
        $basketConfiguration = $this->basketConfigurationRepository->findById($command->getConfigurationId());
        if (null === $basketConfiguration) {
            return;
        }

        // assign required variables
        $locale = new AptoLocale($command->getLocale());
        $connectorConfig = $this->getConnectorConfig($command->getSessionCookies(), $locale);
        $state = new State($command->getState());

        // assert valid values and rules
        $configurableProduct = $this->configurableProductFactory->fromProductId($product->getId());
        $this->valueValidationService->assertValidValues($configurableProduct, $state);
        $this->ruleValidationService->validateState($configurableProduct, $state);

        // update basket configuration
        $basketConfiguration->setProductAndState(
            $product,
            $state
        );
        $basketConfiguration->publishEvents();

        // push basket item
        $this->pushBasketItem(
            $basketConfiguration,
            $connectorConfig,
            $product,
            $locale,
            $command->getQuantity(),
            $command->getPerspectives(),
            $command->getAdditionalData()
        );
    }

    /**
     * @param AddCustomerConfiguration $command
     * @return void
     * @throws AptoJsonSerializerException
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws EmailValidationException
     * @throws InvalidGenderException
     * @throws InvalidUuidException
     */
    public function handleAddCustomerConfiguration(AddCustomerConfiguration $command)
    {
        // get product from repository
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        // get customer from command
        $customerFromCommand = $command->getCustomer();
        if (!$customerFromCommand['externalId']) {
            return;
        }

        // assign required variables
        $locale = new AptoLocale($command->getLocale());
        $connectorConfig = $this->getConnectorConfig($command->getSessionCookies(), $locale);
        $state = new State($command->getState());

        // assert valid values and rules
        $configurableProduct = $this->configurableProductFactory->fromProductId($product->getId());
        $this->valueValidationService->assertValidValues($configurableProduct, $state);
        $this->ruleValidationService->validateState($configurableProduct, $state);

        // get new or existing customer
        $customer = $this->getCustomer(
            $customerFromCommand,
            $connectorConfig->getShopId()
        );

        // create new customer configuration
        $configurationId = $this->customerConfigurationRepository->nextIdentity();
        $customerConfiguration = new CustomerConfiguration(
            $configurationId,
            $product,
            $customer,
            $state
        );
        $customerConfiguration->setName($command->getName());
        $this->customerConfigurationRepository->add($customerConfiguration);
        $customerConfiguration->publishEvents();

        // create tax calculator
        $taxCalculator = new SimpleTaxCalculator(
            (string) $customerConfiguration->getProduct()->getTaxRate(),
            true,
            true
        );
        // @todo create remote customer configuration item
        // get price from customer configuration
        /*
        $configurationPrice = $this->getConfigurationPrice($customerConfiguration, new Currency($connectorConfig->getCurrency()), $customerGroupId, $taxCalculator);

        // format configuration price
        $formattedAmount = $this->getFormattedAmount($configurationPrice);

        */
        throw new Exception('AddCustomerConfiguration is not implemented completely yet.');
    }

    /**
     * @param ConvertBasketToOrderConfiguration $command
     * @return void
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws EmailValidationException
     * @throws InvalidGenderException
     * @throws InvalidUuidException
     * @throws AptoJsonSerializerException
     */
    public function handleConvertBasketToOrderConfiguration(ConvertBasketToOrderConfiguration $command)
    {
        $locale = new AptoLocale($command->getLocale());
        $connectorConfig = $this->getConnectorConfig([], $locale);

        $customer = $this->getCustomer(
            $command->getCustomer(),
            $connectorConfig->getShopId()
        );
        $configurationIds = $command->getConfigurationIds();

        foreach ($configurationIds as $id) {

            // get basket config by id
            $basketConfiguration = $this->basketConfigurationRepository->findById($id);;
            if (null === $basketConfiguration) {
                continue;
            }

            // assert valid values and rules
            $configurableProduct = $this->configurableProductFactory->fromProductId($basketConfiguration->getProduct()->getId());
            $this->valueValidationService->assertValidValues($configurableProduct, $basketConfiguration->getState());
            $this->ruleValidationService->validateState($configurableProduct, $basketConfiguration->getState());

            // create new customer configuration
            // @todo: i changed the id for OrderConfiguration to BasketConfiguration id, maybe not a good choice but for now the shop connector can not update the config id in order details
            $orderConfiguration = new OrderConfiguration(
                $basketConfiguration->getId(),
                $basketConfiguration->getProduct(),
                $customer,
                $basketConfiguration->getState()
            );
            $this->orderConfigurationRepository->add($orderConfiguration);

            // remove old basket config
            $this->basketConfigurationRepository->remove($basketConfiguration);
        }
    }

    /**
     * @param CopyOrderToBasketConfiguration $command
     * @return void
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws InvalidUuidException
     * @throws AptoJsonSerializerException
     */
    public function handleCopyOrderToBasketConfiguration(CopyOrderToBasketConfiguration $command)
    {
        $orderConfiguration = $this->orderConfigurationRepository->findById($command->getOrderConfigurationId());
        if (null === $orderConfiguration) {
            return;
        }

        // assert valid values and rules
        $configurableProduct = $this->configurableProductFactory->fromProductId($orderConfiguration->getProduct()->getId());
        $this->valueValidationService->assertValidValues($configurableProduct, $orderConfiguration->getState());
        $this->ruleValidationService->validateState($configurableProduct, $orderConfiguration->getState());

        $basketConfiguration = new BasketConfiguration(
            new AptoUuid($command->getBasketConfigurationId()),
            $orderConfiguration->getProduct(),
            $orderConfiguration->getState()
        );

        $this->basketConfigurationRepository->add($basketConfiguration);
    }

    /**
     * @param AddProposedConfiguration $command
     * @return void
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws InvalidUuidException
     * @throws AptoJsonSerializerException
     */
    public function handleAddProposedConfiguration(AddProposedConfiguration $command)
    {
        // get product from repository
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        // get id
        $id = ($command->getUuid() != null) ? new AptoUuid($command->getUuid()) : $this->basketConfigurationRepository->nextIdentity();

        // assign required variables
        $state = new State($command->getState());

        // assert valid values and rules
        $configurableProduct = $this->configurableProductFactory->fromProductId($product->getId());
        $this->valueValidationService->assertValidValues($configurableProduct, $state);
        $this->ruleValidationService->validateState($configurableProduct, $state);

        // create new proposed configuration
        $proposedConfiguration = new ProposedConfiguration(
            $id,
            $product,
            $state
        );
        $this->proposedConfigurationRepository->add($proposedConfiguration);
        $proposedConfiguration->publishEvents();
    }

    /**
     * @param AddGuestConfiguration $command
     * @return void
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws EmailValidationException
     * @throws InvalidUuidException
     * @throws AptoJsonSerializerException
     */
    public function handleAddGuestConfiguration(AddGuestConfiguration $command)
    {
        // get product from repository
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        // get id
        $id = $this->guestConfigurationRepository->nextIdentity();
        if ('' !== $command->getId()) {
            $id = new AptoUuid($command->getId());
        }

        // assign required variables
        $state = new State($command->getState());

        // assert valid values and rules
        $configurableProduct = $this->configurableProductFactory->fromProductId($product->getId());
        $this->valueValidationService->assertValidValues($configurableProduct, $state);
        $this->ruleValidationService->validateState($configurableProduct, $state);

        // todo shall we save payload into GuestConfiguration?
        // todo if we save it then we need to update the corresponding twig template to match new humanredable state
        // create new guest configuration
        $guestConfiguration = new GuestConfiguration(
            $id,
            $product,
            $state,
            new Email($command->getEmail()),
            $command->getName()
        );
        $this->guestConfigurationRepository->add($guestConfiguration);
        $guestConfiguration->publishEvents();

        if (!$command->getSendMail()) {
            return;
        }

        // fire GuestConfigurationAdded event
        $this->eventBus->handle(new GuestConfigurationAdded(
            $guestConfiguration->getId(),
            $command->getEmail(),
            $command->getName(),
            $command->getPayload()
        ));
    }

    /**
     * @param AddOfferConfiguration $command
     * @return void
     * @throws AptoJsonSerializerException
     * @throws EmailValidationException
     * @throws InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function handleAddOfferConfiguration(AddOfferConfiguration $command)
    {
        // get product from repository
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        // get id
        $id = $this->guestConfigurationRepository->nextIdentity();

        // assign required variables
        $state = new State($command->getState());

        // assert valid values and rules
        $configurableProduct = $this->configurableProductFactory->fromProductId($product->getId());
        $this->valueValidationService->assertValidValues($configurableProduct, $state);
        $this->ruleValidationService->validateState($configurableProduct, $state);

        // todo shall we save payload into GuestConfiguration?
        // create new guest configuration
        $guestConfiguration = new GuestConfiguration(
            $id,
            $product,
            $state,
            new Email($command->getEmail()),
            $command->getName()
        );
        $this->guestConfigurationRepository->add($guestConfiguration);
        $guestConfiguration->publishEvents();

        // fire GuestConfigurationAdded event
        $this->eventBus->handle(new OfferConfigurationAdded(
            $guestConfiguration->getId(),
            $command->getEmail(),
            $command->getName(),
            $command->getPayload()
        ));
    }

    /**
     * @param AddImmutableConfiguration $command
     * @return void
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws InvalidUuidException
     * @throws AptoJsonSerializerException
     */
    public function handleAddImmutableConfiguration(AddImmutableConfiguration $command)
    {
        // get product from repository
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        // get id
        $id = $this->immutableConfigurationRepository->nextIdentity();
        if ('' !== $command->getId()) {
            $id = new AptoUuid($command->getId());
        }

        // assign required variables
        $state = new State($command->getState());

        // assert valid values and rules
        $configurableProduct = $this->configurableProductFactory->fromProductId($product->getId());
        $this->valueValidationService->assertValidValues($configurableProduct, $state);
        $this->ruleValidationService->validateState($configurableProduct, $state);

        // create new immutable configuration
        $immutableConfiguration = new ImmutableConfiguration(
            $id,
            $product,
            $state
        );
        $this->immutableConfigurationRepository->add($immutableConfiguration);
        $immutableConfiguration->publishEvents();
    }

    /**
     * @param AddCodeConfiguration $command
     * @return void
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws InvalidUuidException
     * @throws AptoJsonSerializerException
     */
    public function handleAddCodeConfiguration(AddCodeConfiguration $command)
    {
        // get product from repository
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        // get id
        $id = $this->codeConfigurationRepository->nextIdentity();
        if (null !== $command->getId()) {
            $id = new AptoUuid($command->getId());
        }

        // assign required variables
        $state = new State($command->getState());

        // assert valid values and rules
        $configurableProduct = $this->configurableProductFactory->fromProductId($product->getId());
        $this->valueValidationService->assertValidValues($configurableProduct, $state);
        $this->ruleValidationService->validateState($configurableProduct, $state);

        // create new guest configuration
        $codeConfiguration = new CodeConfiguration(
            $id,
            $product,
            $state,
            $this->generateUniqueConfigurationCode($id)
        );
        $this->codeConfigurationRepository->add($codeConfiguration);
        $codeConfiguration->publishEvents();
    }

    /**
     * @param AddConfigurationCustomProperty $command
     * @return void
     * @throws AptoCustomPropertyException
     */
    public function handleAddConfigurationCustomProperty(AddConfigurationCustomProperty $command)
    {
        $configuration = null;

        switch ($command->getConfigurationType()) {
            case 'basket': {
                $configuration = $this->basketConfigurationRepository->findById($command->getId());
                break;
            }
            case 'customer': {
                $configuration = $this->customerConfigurationRepository->findById($command->getId());
                break;
            }
            case 'proposed': {
                $configuration = $this->proposedConfigurationRepository->findById($command->getId());
                break;
            }
            case 'guest': {
                $configuration = $this->guestConfigurationRepository->findById($command->getId());
                break;
            }
            case 'immutable': {
                $configuration = $this->immutableConfigurationRepository->findById($command->getId());
                break;
            }
            case 'code': {
                $configuration = $this->codeConfigurationRepository->findById($command->getId());
                break;
            }
        }

        if(null === $configuration) {
            return;
        }

        $configuration->setCustomProperty(
            $command->getCustomerPropertyKey(),
            $command->getCustomerPropertyValue(),
            $command->isTranslatable()
        );

        // toDo: Check if this will work
        switch ($command->getConfigurationType()) {
            case 'basket': {
                $this->basketConfigurationRepository->update($configuration);
                break;
            }
            case 'customer': {
                $this->customerConfigurationRepository->update($configuration);
                break;
            }
            case 'proposed': {
                $this->proposedConfigurationRepository->update($configuration);
                break;
            }
            case 'guest': {
                $this->guestConfigurationRepository->update($configuration);
                break;
            }
            case 'immutable': {
                $this->immutableConfigurationRepository->update($configuration);
                break;
            }
            case 'code': {
                $this->codeConfigurationRepository->update($configuration);
                break;
            }
        }
        $configuration->publishEvents();
    }

    /**
     * @param BasketConfiguration $basketConfiguration
     * @param ConnectorConfig|null $connectorConfig
     * @param Product $product
     * @param AptoLocale $locale
     * @param int $quantity
     * @param array $perspectives
     * @param array $additionalData
     * @return void
     * @throws InvalidUuidException
     */
    protected function pushBasketItem (
        BasketConfiguration $basketConfiguration,
        ?ConnectorConfig $connectorConfig,
        Product $product,
        AptoLocale $locale,
        int $quantity = 1,
        array $perspectives = ['persp1'],
        array $additionalData = []
    ) {
        $basketItem = $this->basketItemFactory->makeBasketItem(
            $basketConfiguration,
            $connectorConfig,
            $product,
            $locale,
            true,
            $perspectives,
            $additionalData,
            true
        );

        $commands = [
            'ConvertToOrderCommand' => 'ConvertBasketToOrderConfiguration'
        ];

        // Shop Connector
        if (null !== $connectorConfig) {
            $this->basketConnector->addItem($basketItem, $connectorConfig, $commands, $quantity);
        }

        // E-Mail
        $this->eventBus->handle(new ConfigurationFinished($basketItem, $quantity));
    }

    /**
     * @param array $customerFromCommand
     * @param string $shopId
     * @return Customer
     * @throws EmailValidationException
     * @throws InvalidGenderException
     * @throws InvalidUuidException
     */
    protected function getCustomer(array $customerFromCommand, string $shopId): Customer
    {
        $customer = $this->customerRepository->findOneByShopAndExternalId(
            $shopId,
            $customerFromCommand['externalId']
        );

        $userName = new CustomerUserName($customerFromCommand['email'] . '-' . $customerFromCommand['externalId']);
        $email = new Email($customerFromCommand['email']);

        // create customer, if not existing
        if (null === $customer) {
            $newCustomerId = $this->customerRepository->nextIdentity();
            $customer = new Customer(
                $newCustomerId,
                $userName,
                $email,
                new AptoUuid($shopId),
                $customerFromCommand['externalId']
            );
            $this->customerRepository->add($customer);
        }

        // update data
        $customer
            ->setUsername($userName)
            ->setEmail($email)
            ->setFirstName($customerFromCommand['firstName'])
            ->setLastName($customerFromCommand['lastName'])
            ->setGender($this->getGender($customerFromCommand['gender']));

        return $customer;
    }

    /**
     * @param string $value
     * @return Gender
     * @throws InvalidGenderException
     */
    protected function getGender(string $value): Gender
    {
        switch (strtolower(substr($value, 0, 1))) {
            case 'm':
                $gender = Gender::MALE;
                break;

            case 'f':
            case 'w':
                $gender = Gender::FEMALE;
                break;

            default:
                $gender = Gender::UNKNOWN;
        }

        return new Gender($gender);
    }

    /**
     * @param array $sessionCookies
     * @param AptoLocale|null $locale
     * @return ConnectorConfig|null
     */
    protected function getConnectorConfig(array $sessionCookies = [], AptoLocale $locale = null)
    {
        $connectorConfig = $this->shopRepository->findConnectorConfigByDomain($this->requestSessionStore->getHttpHost());

        if ($connectorConfig['connectorUrl'] instanceof AptoTranslatedValue) {
            $connectorConfig['connectorUrl'] = $connectorConfig['connectorUrl']->getTranslation($locale)->getValue();
        }
        if ($connectorConfig['connectorUrl'] === '' || $connectorConfig['connectorToken'] === null) {
            return null;
        }

        return ConnectorConfig::fromArray(
            $connectorConfig,
            $sessionCookies
        );
    }

    /**
     * @return AptoUuid
     */
    protected function nextConfigurationId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @param AptoUuid $id
     * @return string
     */
    protected function generateUniqueConfigurationCode(AptoUuid $id): string
    {
        $code = $this->getRandomCode(5);

        while (false === $this->codeConfigurationRepository->isCodeUnique($id->getId(), $code)) {
            $code = $this->getRandomCode(5);
        }

        return $code;
    }

    /**
     * @param int $length
     * @return string
     */
    protected function getRandomCode(int $length): string
    {
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $numberOrLetter = rand(0, 1);
            $character = 48;

            if ($numberOrLetter === 0) {
                $character = rand(48, 57);
            }

            if ($numberOrLetter === 1) {
                $character = rand(97, 122);
            }

            $code .= chr($character);
        }

        return $code;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddBasketConfiguration::class => [
            'method' => 'handleAddBasketConfiguration',
            'bus' => 'command_bus'
        ];

        yield UpdateBasketConfiguration::class => [
            'method' => 'handleUpdateBasketConfiguration',
            'bus' => 'command_bus'
        ];

        yield AddCustomerConfiguration::class => [
            'method' => 'handleAddCustomerConfiguration',
            'bus' => 'command_bus'
        ];

        yield ConvertBasketToOrderConfiguration::class => [
            'method' => 'handleConvertBasketToOrderConfiguration',
            'bus' => 'command_bus'
        ];

        yield CopyOrderToBasketConfiguration::class => [
            'method' => 'handleCopyOrderToBasketConfiguration',
            'bus' => 'command_bus'
        ];

        yield AddProposedConfiguration::class => [
            'method' => 'handleAddProposedConfiguration',
            'bus' => 'command_bus'
        ];

        yield AddGuestConfiguration::class => [
            'method' => 'handleAddGuestConfiguration',
            'bus' => 'command_bus'
        ];

        yield AddOfferConfiguration::class => [
            'method' => 'handleAddOfferConfiguration',
            'bus' => 'command_bus'
        ];

        yield AddImmutableConfiguration::class => [
            'method' => 'handleAddImmutableConfiguration',
            'bus' => 'command_bus'
        ];

        yield AddCodeConfiguration::class => [
            'method' => 'handleAddCodeConfiguration',
            'bus' => 'command_bus'
        ];

        yield AddConfigurationCustomProperty::class => [
            'method' => 'handleAddConfigurationCustomProperty',
            'bus' => 'command_bus'
        ];
    }
}
