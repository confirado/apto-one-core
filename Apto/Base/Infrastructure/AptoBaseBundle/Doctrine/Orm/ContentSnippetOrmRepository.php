<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippet;
use Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippetRepository;
use \Psr\Cache\InvalidArgumentException;

class ContentSnippetOrmRepository extends AptoOrmRepository implements ContentSnippetRepository
{
    const ENTITY_CLASS = ContentSnippet::class;

    /**
     * @param ContentSnippet $model
     * @return void
     * @throws InvalidArgumentException
     */
    public function update(ContentSnippet $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
        $this->invalidateCache();
    }

    /**
     * @param ContentSnippet $model
     * @return void
     * @throws InvalidArgumentException
     */
    public function add(ContentSnippet $model)
    {
        $this->_em->persist($model);
        $this->_em->flush();
        $this->invalidateCache();
    }

    /**
     * @param ContentSnippet $model
     * @return void
     * @throws InvalidArgumentException
     */
    public function remove(ContentSnippet $model)
    {
        $this->_em->remove($model);
        $this->invalidateCache();
    }

    /**
     * @param mixed|string $id
     * @return ContentSnippet|mixed|null|object
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('ContentSnippet')
            ->where('ContentSnippet.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $name
     * @return ContentSnippet|mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByName(string $name)
    {
        $builder = $this->createQueryBuilder('ContentSnippet')
            ->where('ContentSnippet.name = :name')
            ->setParameter('name', $name);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $parentId
     * @return bool
     */
    public function hasChildren(string $parentId)
    {
        $dql = 'SELECT
                  c.id.id as id
              FROM
                  ' . $this->getEntityName() . ' c
              LEFT JOIN
                  c.parent cp
              WHERE
                  cp.id.id = :parentId';
        $query = $this->_em->createQuery($dql)
            ->setParameter('parentId', $parentId);

        $result = $query->getScalarResult();
        return (sizeof($result) > 0);
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function invalidateCache()
    {
        AptoCacheService::deleteItem('ContentSnippetTree-Frontend');
        AptoCacheService::deleteItem('ContentSnippetTree-Backend');
    }
}
