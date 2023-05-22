<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Dql\ProductDqlService;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Cache\Adapter\ApcuAdapter;

class ProductOrmRepository extends AptoOrmRepository implements ProductRepository
{
    const ENTITY_CLASS = Product::class;

    /**
     * @param Product $model
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function update(Product $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
        $this->invalidateCache($model->getId()->getId(), $model->getSeoUrl());
    }

    /**
     * @param Product $model
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function add(Product $model)
    {
        $this->_em->persist($model);
        $this->invalidateCache($model->getId()->getId());
    }

    /**
     * @param Product $model
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function remove(Product $model)
    {
        $this->_em->remove($model);
        $this->invalidateCache($model->getId()->getId());
    }

    /**
     * @param mixed $id
     * @return Product|mixed|null|object
     * @throws NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('Product')
            ->where('Product.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Identifier $identifier
     * @return Product|mixed|null
     * @throws NonUniqueResultException
     */
    public function findByIdentifier(Identifier $identifier)
    {
        $builder = $this->createQueryBuilder('Product')
            ->where('Product.identifier.value = :identifier')
            ->setParameter('identifier', $identifier->getValue());

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findSectionsElementsAsArray(string $id)
    {
        $dqlService = new ProductDqlService($this->_em, $this->_entityName);
        return $dqlService->findSectionsElements($id);
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findProductCustomPropertiesAsArray(array $ids)
    {
        $dqlService = new ProductDqlService($this->_em, $this->_entityName);
        return $dqlService->findProductCustomProperties($ids);
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findSectionCustomPropertiesAsArray(array $ids)
    {
        $dqlService = new ProductDqlService($this->_em, $this->_entityName);
        return $dqlService->findSectionCustomProperties($ids);
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findElementCustomPropertiesAsArray(array $ids)
    {
        $dqlService = new ProductDqlService($this->_em, $this->_entityName);
        return $dqlService->findElementCustomProperties($ids);
    }

    /**
     * @param null $entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush($entity = null)
    {
        $this->_em->flush();
    }

    /**
     * @param string $id
     * @return int
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function findNextSectionPosition(string $id)
    {
        $dqlService = new ProductDqlService($this->_em, $this->_entityName);
        return $dqlService->findNextSectionPosition($id);
    }

    /**
     * @param string $productId
     * @param string $sectionId
     * @return int
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function findNextElementPosition(string $productId, string $sectionId)
    {
        $dqlService = new ProductDqlService($this->_em, $this->_entityName);
        return $dqlService->findNextElementPosition($productId, $sectionId);
    }

    /**
     * @param string $productId
     * @param string|null $seoUrl
     * @return mixed|void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function invalidateCache(string $productId, ?string $seoUrl = null)
    {
        AptoCacheService::clearCache('ProductList-');

        self::invalidateCacheItem($productId);

        if ($seoUrl) {
            self::invalidateCacheItem($seoUrl);
        }
    }

    /**
     * @param string $identifier
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected static function invalidateCacheItem(string $identifier)
    {
        $keys = [
            'AptoProduct-',
            'AptoProduct-Sections-',
            'AptoProduct-Sections-Elements-',
            'Configurable-Product-wRules-wComputedValues-',
            'Configurable-Product-wRules-woComputedValues-',
            'Configurable-Product-woRules-wComputedValues-',
            'Configurable-Product-woRules-woComputedValues-',
            'Configurable-Product-Raw-'
        ];

        foreach ($keys as $key) {
            AptoCacheService::deleteItem($key . $identifier);
        }
    }
}
