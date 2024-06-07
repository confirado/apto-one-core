<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Category\CategoryFinder;
use Apto\Catalog\Domain\Core\Model\Category\Category;

class CategoryOrmFinder extends AptoOrmFinder implements CategoryFinder
{
    const ENTITY_CLASS = Category::class;
    const MODEL_VALUES = [
        ['id.id', 'id'],
        'name',
        'description',
        'created',
        'position',
        'parentId'
    ];

    const MODEL_POST_PROCESSES = [
        'name' => [DqlQueryBuilder::class, 'decodeJson'],
        'description' => [DqlQueryBuilder::class, 'decodeJson'],
        'position' => [DqlQueryBuilder::class, 'decodeInteger']
    ];

    /**
     * @param string $id
     * @return array|null
     * @throws \Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'c' => self::MODEL_VALUES,
                'cp' => [
                    ['id.id', 'id']
                ],
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
                'c' => [
                    ['parent', 'cp', 'id'],
                    ['previewImage', 'm', 'id']
                ]
            ])
            ->setPostProcess([
                'c' => self::MODEL_POST_PROCESSES
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);
        if (array_key_exists(0, $result['parent']) && array_key_exists('id',$result['parent'][0])) {
            $result['parent'] = $result['parent'][0]['id'];
        }
        else {
            $result['parent'] = null;
        }
        if($result !== null) {
            if(isset($result['previewImage'][0])) {
                $result['previewImageMediaFile'] = $result['previewImage'][0];
                $result['previewImage'] = $result['previewImageMediaFile']['path'] . '/' . $result['previewImageMediaFile']['filename'] . '.' . $result['previewImageMediaFile']['extension'];
            } else {
                $result['previewImageMediaFile'] = $result['previewImage'];
                $result['previewImage'] = '';
            }
        }
        return $result;
    }

    /**
     * @param string $searchString
     * @return array
     */
    public function findCategories(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'c' => self::MODEL_VALUES,
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
                'c' => [
                    ['previewImage', 'm', 'id']
                ]
            ])
            ->setSearch([
                'c' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setOrderBy([
                ['c.created', 'DESC']
            ])
            ->setPostProcess([
                'c' => self::MODEL_POST_PROCESSES
            ]);

        $results = $builder->getResult($this->entityManager);

        foreach ($results['data'] as &$result) {
            if(isset($result['previewImage'][0])) {
                $result['previewImageMediaFile'] = $result['previewImage'][0];
                $result['previewImage'] = $result['previewImageMediaFile']['path'] . '/' . $result['previewImageMediaFile']['filename'] . '.' . $result['previewImageMediaFile']['extension'];
            } else {
                $result['previewImageMediaFile'] = $result['previewImage'];
                $result['previewImage'] = '';
            }
        }

        return $results;
    }

    /**
     * @param string $searchString
     * @return array
     * @throws \Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException
     */
    public function findCategoryTree(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'c' => self::MODEL_VALUES,
            ])
            ->setPostProcess([
                'c' => self::MODEL_POST_PROCESSES
            ])
            /* @todo convertFlatToTree cant handle root entries with parent id, if you search for a child and no root entry matches result will be empty
            ->setSearch([
                'c' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            */
            ->setOrderBy([
                ['c.name', 'ASC']
            ]);

        $flatResult = $builder->getResult($this->entityManager);

        return $this->convertFlatToTree($flatResult['data']);
    }

    /**
     * @param $flatResult
     * @param null $rootId
     * @return array
     */
    protected function convertFlatToTree($flatResult, $rootId = null)
    {
        // init tree array
        $tree = [];

        // populate current root level
        foreach ($flatResult as $key => $entry) {
            if ($entry['parentId'] == $rootId) {
                $tree[] = $entry;
                unset ($flatResult[$key]);
            }
        }

        // check for children and run makeTree recursively
        foreach ($tree as $rootKey => $rootEntry) {
            foreach ($flatResult as $key => $entry) {
                if ($entry['parentId'] ==  $rootEntry['surrogateId']) {
                    $tree[$rootKey]['children'] = $this->convertFlatToTree($flatResult, $entry['parentId']);
                }
            }
        }

        // return tree array
        return $tree;
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findCustomProperties(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'c' => [
                ],
                'cp' => [
                    ['id.id', 'id'],
                    'surrogateId',
                    'key',
                    'value',
                    'translatable',
                    'productConditionId'
                ]
            ])
            ->setJoins([
                'c' => [
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                'cp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }
}
