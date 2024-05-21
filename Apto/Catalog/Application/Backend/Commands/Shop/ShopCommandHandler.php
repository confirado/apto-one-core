<?php

namespace Apto\Catalog\Application\Backend\Commands\Shop;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\EmailOptional;
use Apto\Base\Domain\Core\Model\Language\LanguageRepository;
use Apto\Catalog\Domain\Core\Model\Category\CategoryRepository;
use Apto\Catalog\Domain\Core\Model\Shop\Shop;
use Apto\Base\Domain\Core\Model\Shop\ShopRemoved;
use Apto\Catalog\Domain\Core\Model\Shop\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Money\Currency;

use Apto\Base\Domain\Core\Model\EmailValidationException;

class ShopCommandHandler extends AbstractCommandHandler
{
    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var LanguageRepository
     */
    protected $languageRepository;

    /**
     * ShopCommandHandler constructor.
     * @param ShopRepository $shopRepository
     * @param CategoryRepository $categoryRepository
     * @param LanguageRepository $languageRepository
     */
    public function __construct(ShopRepository $shopRepository, CategoryRepository $categoryRepository, LanguageRepository $languageRepository)
    {
        $this->shopRepository = $shopRepository;
        $this->categoryRepository = $categoryRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param AddShop $command
     * @throws EmailValidationException
     */
    public function handleAddShop(AddShop $command)
    {
        $shop = new Shop(
            $this->shopRepository->nextIdentity(),
            $command->getName(),
            $command->getDomain()
        );
        $shop
            ->setDescription($command->getDescription())
            ->setCategories($this->buildCategoryCollection($command->getCategories()))
            ->setLanguages($this->buildLanguageCollection($command->getLanguages()))
            ->setConnectorUrl($this->getTranslatedValue($command->getConnectorUrl()))
            ->setConnectorToken($command->getConnectorToken())
            ->setTemplateId($command->getTemplateId())
            ->setOperatorName($command->getOperatorName())
            ->setCurrency(new Currency($command->getCurrency()))
            ->setOperatorEmail(new EmailOptional($command->getOperatorEmail() ?: null))
        ;

        $this->shopRepository->add($shop);
        $shop->publishEvents();
    }

    /**
     * @param UpdateShop $command
     * @throws EmailValidationException
     */
    public function handleUpdateShop(UpdateShop $command)
    {
        $shop = $this->shopRepository->findById($command->getId());

        if (null !== $shop) {
            $shop->setName($command->getName())
                ->setDomain($command->getDomain())
                ->setConnectorUrl($this->getTranslatedValue($command->getConnectorUrl()))
                ->setConnectorToken($command->getConnectorToken())
                ->setTemplateId($command->getTemplateId())
                ->setOperatorName($command->getOperatorName())
                ->setCurrency(new Currency($command->getCurrency()))
                ->setDescription($command->getDescription())
                ->setCategories($this->buildCategoryCollection($command->getCategories()))
                ->setLanguages($this->buildLanguageCollection($command->getLanguages()))
                ->setOperatorEmail(new EmailOptional($command->getOperatorEmail() ?: null))
            ;

            $this->shopRepository->update($shop);
            $shop->publishEvents();
        }
    }

    /**
     * @param UpdateShopDomain $command
     */
    public function handleUpdateShopDomain(UpdateShopDomain $command)
    {
        $shop = $this->shopRepository->findById($command->getId());
        if (null === $shop) {
            return;
        }

        $shop->setDomain($command->getDomain());
        $shop->publishEvents();
        $this->shopRepository->update($shop);
    }

    /**
     * @param UpdateShopName $command
     */
    public function handleUpdateShopName(UpdateShopName $command)
    {
        $shop = $this->shopRepository->findById($command->getId());
        if (null === $shop) {
            return;
        }

        $shop->setName($command->getName());
        $shop->publishEvents();
        $this->shopRepository->update($shop);
    }

    /**
     * @param UpdateShopOperator $command
     * @return void
     * @throws EmailValidationException
     */
    public function handleUpdateShopOperator(UpdateShopOperator $command)
    {
        $shop = $this->shopRepository->findById($command->getId());
        if (null === $shop) {
            return;
        }

        $shop
            ->setOperatorName($command->getName())
            ->setOperatorEmail(new EmailOptional($command->getMail() ?: null))
            ->publishEvents()
        ;
        $this->shopRepository->update($shop);
    }

    /**
     * @param RemoveShop $command
     */
    public function handleRemoveShop(RemoveShop $command)
    {
        $shop = $this->shopRepository->findById($command->getId());

        if(null !== $shop) {
            $this->shopRepository->remove($shop);
            DomainEventPublisher::instance()->publish(
                new ShopRemoved(
                    $shop->getId()
                )
            );
        }
    }

    /**
     * @param AddShopCustomProperty $command
     * @throws AptoCustomPropertyException
     */
    public function handleAddShopCustomProperty(AddShopCustomProperty $command)
    {
        $shop = $this->shopRepository->findById($command->getShopId());

        if (null === $shop) {
            return;
        }

        $shop->setCustomProperty(
            $command->getKey(),
            $command->getValue(),
            $command->getTranslatable()
        );

        $this->shopRepository->update($shop);
        $shop->publishEvents();
    }

    /**
     * @param RemoveShopCustomProperty $command
     */
    public function handleRemoveShopCustomProperty(RemoveShopCustomProperty $command)
    {
        $shop = $this->shopRepository->findById($command->getShopId());

        if (null === $shop) {
            return;
        }

        $shop->removeCustomProperty(
            new AptoUuid($command->getId())
        );

        $this->shopRepository->update($shop);
        $shop->publishEvents();
    }

    /**
     * @param array $categories
     * @return ArrayCollection
     */
    protected function buildCategoryCollection(array $categories)
    {
        $categoryCollection = new ArrayCollection();
        foreach ($categories as $category) {
            $categoryModel = $this->categoryRepository->findById($category['id']);
            $categoryCollection->add($categoryModel);
        }

        return $categoryCollection;
    }

    /**
     * @param array $languages
     * @return ArrayCollection
     */
    protected function buildLanguageCollection(array $languages)
    {
        $languageCollection = new ArrayCollection();
        foreach ($languages as $language) {
            $languageModel = $this->languageRepository->findById($language['id']);
            $languageCollection->add($languageModel);#
        }

        return $languageCollection;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddShop::class => [
            'method' => 'handleAddShop',
            'bus' => 'command_bus'
        ];

        yield UpdateShop::class => [
            'method' => 'handleUpdateShop',
            'bus' => 'command_bus'
        ];

        yield UpdateShopDomain::class => [
            'method' => 'handleUpdateShopDomain',
            'bus' => 'command_bus'
        ];

        yield UpdateShopName::class => [
            'method' => 'handleUpdateShopName',
            'bus' => 'command_bus'
        ];

        yield UpdateShopOperator::class => [
            'method' => 'handleUpdateShopOperator',
            'bus' => 'command_bus'
        ];

        yield RemoveShop::class => [
            'method' => 'handleRemoveShop',
            'bus' => 'command_bus'
        ];

        yield AddShopCustomProperty::class => [
            'method' => 'handleAddShopCustomProperty',
            'bus' => 'command_bus'
        ];

        yield RemoveShopCustomProperty::class => [
            'method' => 'handleRemoveShopCustomProperty',
            'bus' => 'command_bus'
        ];
    }
}
