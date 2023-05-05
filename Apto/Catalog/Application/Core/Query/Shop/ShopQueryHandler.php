<?php

namespace Apto\Catalog\Application\Core\Query\Shop;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;

class ShopQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ShopFinder
     */
    private $shopFinder;

    /**
     * FindShopHandler constructor.
     * @param ShopFinder $shopFinder
     */
    public function __construct(ShopFinder $shopFinder)
    {
        $this->shopFinder = $shopFinder;
    }

    /**
     * @param FindShop $query
     * @return mixed
     */
    public function handleFindShop(FindShop $query)
    {
        return $this->shopFinder->findById($query->getId());
    }

    /**
     * @param FindShopByDomain $query
     * @return mixed
     */
    public function handleFindShopByDomain(FindShopByDomain $query)
    {
        return $this->shopFinder->findByDomain($query->getDomain());
    }

    /**
     * @param FindShopContext $query
     * @return mixed
     */
    public function handleFindShopContext(FindShopContext $query)
    {
        $shop = $this->shopFinder->findContextByDomain($query->getDomain());


        return $shop;
    }

    /**
     * @param FindShops $query
     * @return mixed
     */
    public function handleFindShops(FindShops $query)
    {
        return $this->shopFinder->findShops($query->getSearchString());
    }

    /**
     * @param FindShopCustomProperties $query
     * @return array|null
     */
    public function handleFindShopCustomProperties(FindShopCustomProperties $query)
    {
        return $this->shopFinder->findCustomProperties($query->getId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindShop::class => [
            'method' => 'handleFindShop',
            'bus' => 'query_bus'
        ];

        yield FindShopByDomain::class => [
            'method' => 'handleFindShopByDomain',
            'bus' => 'query_bus'
        ];

        yield FindShopContext::class => [
            'method' => 'handleFindShopContext',
            'bus' => 'query_bus'
        ];

        yield FindShops::class => [
            'method' => 'handleFindShops',
            'bus' => 'query_bus'
        ];

        yield FindShopCustomProperties::class => [
            'method' => 'handleFindShopCustomProperties',
            'bus' => 'query_bus'
        ];
    }
}
