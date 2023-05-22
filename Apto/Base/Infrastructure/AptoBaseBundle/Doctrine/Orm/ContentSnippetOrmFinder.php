<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Query\ContentSnippet\ContentSnippetFinder;
use Apto\Base\Application\Core\Service\ContentSnippet\ContentSnippetProvider;
use Apto\Base\Application\Core\Service\ContentSnippet\ContentSnippetRegistry;
use Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippet;
use Doctrine\ORM\EntityManagerInterface;

class ContentSnippetOrmFinder extends AptoOrmFinder implements ContentSnippetFinder
{
    const ENTITY_CLASS = ContentSnippet::class;

    const MODEL_VALUES = [
        ['id.id', 'id'],
        'name',
        'active',
        'content',
        'html',
        'created'
    ];

    const MODEL_POST_PROCESSES = [
        'active' => [DqlQueryBuilder::class, 'decodeBool'],
        'content' => [DqlQueryBuilder::class, 'decodeJson'],
        'html' => [DqlQueryBuilder::class, 'decodeBool']
    ];

    /**
     * @var ContentSnippetRegistry
     */
    private ContentSnippetRegistry $contentSnippetRegistry;

    /**
     * decode a given value from decimal string by removing trailing zeros
     * @param $value
     * @return mixed
     */
    public static function decodeSerialized($value)
    {
        return unserialize($value);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param ContentSnippetRegistry $contentSnippetRegistry
     */
    public function __construct(EntityManagerInterface $entityManager, ContentSnippetRegistry $contentSnippetRegistry)
    {
        parent::__construct($entityManager);
        $this->contentSnippetRegistry = $contentSnippetRegistry;
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findContentSnippets(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'c' => self::MODEL_VALUES
            ])
            ->setPostProcess([
                'c' => self::MODEL_POST_PROCESSES
            ])
            ->setSearch([
                'c' => [
                    'id.id',
                    'name',
                    'content'
                ]
            ], $searchString)
            ->setOrderBy([
                ['c.created', 'ASC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findById(string $id)
    {
        return $this->findByProperty('id.id', $id);
    }

    /**
     * @param string $parentId
     * @return array
     * @throws DqlBuilderException
     */
    public function findChildren(string $parentId = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'c' => self::MODEL_VALUES,
                'cp' => [
                    ['id.id', 'id']
                ]
            ])
            ->setPostProcess([
                'c' => self::MODEL_POST_PROCESSES
            ])
            // SET JOIN
            ->setJoins([
                'c' => [
                    ['parent', 'cp', 'id']
                ]
            ])
            ->setWhere(
                'cp.id.id = :parentId', ['parentId' => $parentId]
            )
            ->setOrderBy([
                ['c.created', 'ASC']
            ])
            ;

        return $builder->getResult($this->entityManager, true);
    }

    /**
     * @param bool $frontend
     * @param string $domain
     * @param bool $indexedFrontendTree
     * @return array
     * @throws DqlBuilderException
     */
    public function getTree(bool $frontend, string $domain, bool $indexedFrontendTree = false): array
    {
        $flatResultBuilder = $this->getFlatResultBuilder();
        $rootId = -1;
        $flatResult = $flatResultBuilder->getResult($this->entityManager);
        $contentSnippets = $this->addRootContentSnippet($flatResult['data'], $rootId);
        $contentSnippets = $this->setHasChildren($contentSnippets);

        if ($frontend) {
            // create frontend tree
            $result = $this->makeFrontendTree($contentSnippets);
            $inactive = $result['inactive'];
            $tree = $result['tree']['root'];
            $tree = $this->mergeFrontendTree($tree);
            $tree = $this->applyInactiveContentSnippets($tree, $inactive);
            $tree = $this->mergeFrontendDomainTree($tree, $domain);

            if ($indexedFrontendTree) {
                $tree = $this->frontendTreeToIndexedTree($tree);
            }
        } else {
            // create backend tree
            $groupedContentSnippets = $this->groupItemsByParentId($contentSnippets);
            $tree = $this->makeBackendTree($groupedContentSnippets, [$contentSnippets[0]], $rootId);
            $tree = $tree[0]['children'];
        }
        return $tree;
    }

    /**
     * @param array $assocTree
     * @return array
     */
    private function frontendTreeToIndexedTree(array $assocTree): array
    {
        $indexedTree = $this->convertAssocTreeToIndexedTree($assocTree);
        $this->applyContentToIndexedTree($indexedTree, $assocTree);
        return $indexedTree;
    }

    /**
     * @param array $tree
     * @return array
     */
    private function convertAssocTreeToIndexedTree(array $tree): array
    {
        $result = [];

        foreach ($tree as $name => $content) {
            if (!is_array($content)) {
                continue;
            }

            $contentSnippet = [
                'name' => $name,
                'content' => [],
                'children' => $content
            ];

            if (count($contentSnippet['children']) > 0) {
                $contentSnippet['children'] = $this->convertAssocTreeToIndexedTree($content);
            }

            $result[] = $contentSnippet;
        }

        return $result;
    }

    /**
     * @param array $indexedTree
     * @param array $assocArray
     * @return void
     */
    private function applyContentToIndexedTree(array &$indexedTree, array &$assocArray)
    {
        foreach($indexedTree as $key => &$value) {
            if(count($value['children']) > 0) {
                $this->applyContentToIndexedTree($value['children'], $assocArray[$value['name']]);
            } else {
                unset($value['children']);
                $value['content'] = $assocArray[$value['name']];
            }
        }
    }

    /**
     * @return array
     * @throws DqlBuilderException
     */
    public function getExportItems(): array
    {
        /** TODO: Reduce amount of iterations */
        $result = [];
        $flatResult = $this->getFlatResultBuilder()->getResult($this->entityManager)['data'];
        foreach ($flatResult as $snippet) {
            if (!$this->hasChildren($flatResult, $snippet['surrogateId'])) {
                $result[$snippet['id']]['value'] = $snippet['content'];
                $result[$snippet['id']]['snippetPath'] = $this->getSnippetPath($flatResult, $snippet['surrogateId']);
            }
        }
        uasort($result, function($a, $b) {
            return $a['snippetPath'] <=> $b['snippetPath'];
        });
        return $result;
    }

    /**
     * @param string $property
     * @param string $value
     * @return array|null
     * @throws DqlBuilderException
     */
    protected function findByProperty(string $property, string $value): ?array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty($property, $value)
            ->setValues([
                'c' => self::MODEL_VALUES,
                'cp' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'c' => [
                    ['parent', 'cp', 'id']
                ]
            ])
            ->setPostProcess([
                'c' => self::MODEL_POST_PROCESSES
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);
        if (array_key_exists(0, $result['parent']) && array_key_exists('id',$result['parent'][0])) {
            $result['parent'] = $result['parent'][0]['id'];
        } else {
            $result['parent'] = null;
        }
        return $result;
    }

    /**
     * @return DqlQueryBuilder
     */
    protected function getFlatResultBuilder(): DqlQueryBuilder
    {
        $values = self::MODEL_VALUES;
        $values[] = 'parentId';

        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'c' => $values
            ])
            ->setPostProcess([
                'c' => self::MODEL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['c.name', 'ASC']
            ]);

        return $builder;
    }

    /**
     * @param array $items
     * @param int $rootId
     * @return array
     */
    protected function addRootContentSnippet(array $items, int $rootId): array
    {
        $contentSnippets = [
            ['surrogateId' => $rootId, 'active' => true, 'name' => 'root', 'parentId' => null]
        ];

        foreach ($items as $item) {
            if (null === $item['parentId']) {
                $item['parentId'] = $rootId;
            }
            $contentSnippets[] = $item;
        }

        return $contentSnippets;
    }

    /**
     * @param array $contentSnippets
     * @return array
     */
    protected function makeFrontendTree(array $contentSnippets): array
    {
        // prepare content snippets
        $tree = [];
        $inactive = [];

        $contentSnippets = $this->addContentSnippetPaths(
            $this->indexItemsById($contentSnippets)
        );

        // sort desc by path deep, important for non active removing
        usort($contentSnippets, function ($a, $b) {
            $aPathLength = count($a['path']);
            $bPathLength = count($b['path']);

            if ($aPathLength < $bPathLength) {
                return 1;
            }

            if ($aPathLength > $bPathLength) {
                return -1;
            }

            return 0;
        });

        // build content snippet tree
        foreach ($contentSnippets as $contentSnippet) {
            $currentNode = &$tree;

            // create array path deep
            foreach ($contentSnippet['path'] as $index => $segment) {
                if (!array_key_exists($segment, $currentNode)) {
                    $currentNode[$segment] = [];
                }

                // add or remove content snippet at the last path entry
                if ($index === count($contentSnippet['path']) - 1) {
                    if (false === $contentSnippet['active']) {
                        // remove content snippet if its not active
                        $inactive[] = $contentSnippet['path'];
                        unset($currentNode[$segment]);
                    } elseif (
                        array_key_exists('content', $contentSnippet) &&
                        false === $contentSnippet['hasChildren']
                    ) {
                        // if set add content snippet content
                        $currentNode[$segment] = $contentSnippet['content'];
                    }
                } else {
                    // point current node to current path segment
                    $currentNode = &$currentNode[$segment];
                }
            }
        }

        return [
            'tree' => $tree,
            'inactive' => $inactive
        ];
    }

    /**
     * @param array $contentSnippets
     * @return array
     */
    protected function setHasChildren(array $contentSnippets): array
    {
        $parentIds = [];
        foreach ($contentSnippets as $contentSnippet) {
            if (null === $contentSnippet['parentId']) {
                continue;
            }
            $parentIds[$contentSnippet['parentId']] = true;
        }

        foreach ($contentSnippets as &$contentSnippet) {
            $contentSnippet['hasChildren'] = false;
            if (array_key_exists($contentSnippet['surrogateId'], $parentIds)) {
                $contentSnippet['hasChildren'] = true;
            }
        }
        return $contentSnippets;
    }

    /**
     * @param array $items
     * @return array
     */
    protected function indexItemsById(array $items): array
    {
        $indexedItems = [];
        foreach ($items as $item) {
            $indexedItems[$item['surrogateId']] = $item;
        }
        return $indexedItems;
    }

    /**
     * @param array $contentSnippets
     * @return array
     */
    protected function addContentSnippetPaths(array $contentSnippets): array
    {
        foreach ($contentSnippets as &$contentSnippet) {
            $contentSnippet['path'] = $this->getSnippetPathArray($contentSnippets, $contentSnippet['surrogateId']);
        }
        return $contentSnippets;
    }

    /**
     * @param array $items
     * @param int $surrogateId
     * @return array
     */
    protected function getSnippetPathArray(array $items, int $surrogateId): array
    {
        $item = $items[$surrogateId];

        if ($item['parentId'] !== null) {
            return array_merge($this->getSnippetPathArray($items, $item['parentId']), [$item['name']]);
        } else {
            return [$item['name']];
        }
    }

    /**
     * @param array $frontendSnippets
     * @return array
     */
    protected function mergeFrontendTree(array $frontendSnippets): array
    {
        $basicContentSnippetJsonArray = [];
        $contentSnippetProviders = $this->contentSnippetRegistry->getContentSnippetProviders();

        /** @var ContentSnippetProvider $provider */
        foreach ($contentSnippetProviders as $provider) {
            $basicContentSnippetJsonArray[] = $provider->getContentSnippetsJson();
        }

        foreach ($basicContentSnippetJsonArray as $jsonString) {
            $basicContentSnippetArray = json_decode($jsonString, true);
            if (is_array($basicContentSnippetArray)) {
                $frontendSnippets = array_replace_recursive($basicContentSnippetArray, $frontendSnippets);
            }
        }
        return $frontendSnippets;
    }

    /**
     * @param array $tree
     * @param array $inactive
     * @return array
     */
    protected function applyInactiveContentSnippets(array $tree, array $inactive): array
    {
        $tree['root'] = $tree;

        foreach ($inactive as $path) {
            $currentNode = &$tree;

            foreach ($path as $index => $segment) {
                // if path already not in tree we can ignore that path
                if (!array_key_exists($segment, $currentNode)) {
                    break;
                }

                if ($index === count($path) - 1) {
                    // remove content snippet at the last path entry
                    unset($currentNode[$segment]);
                } else {
                    // point current node to current path segment
                    $currentNode = &$currentNode[$segment];
                }
            }
        }

        return $tree['root'];
    }

    /**
     * @param array $frontendSnippets
     * @param string $domain
     * @return array
     */
    protected function mergeFrontendDomainTree(array $frontendSnippets, string $domain): array
    {
        $domainSnippets = [];
        foreach ($frontendSnippets as $key => $frontendSnippet) {
            if ($key === '_' . str_replace('.', '-', $domain)) {
                $domainSnippets = $frontendSnippet;
                unset($frontendSnippets[$key]);
                break;
            }
        }
        return array_replace_recursive($frontendSnippets, $domainSnippets);
    }

    /**
     * @param array $items
     * @return array
     */
    protected function groupItemsByParentId(array $items): array
    {
        $groupedItems = [];

        foreach ($items as $item)
        {
            $groupedItems[$item['parentId']][] = $item;
        }

        return$groupedItems;
    }

    /**
     * @param array $items
     * @param array $parent
     * @param int $rootId
     * @return array
     */
    protected function makeBackendTree(array &$items, array $parent, int $rootId): array
    {
        $contentSnippetTree = [];
        foreach ($parent as $item) {
            $surrogateId = $item['surrogateId'];

            if (isset($items[$surrogateId])) {
                $item['content'] = [];
                $item['children'] = $this->makeBackendTree($items, $items[$surrogateId], $rootId);
            }

            if ($item['parentId'] === $rootId) {
                $item['parentId'] = null;
            }

            $contentSnippetTree[] = $item;
        }

        return $contentSnippetTree;
    }

    /**
     * @param $flatResult
     * @param $snippetId
     * @param string $currentPath
     * @return mixed|string
     */
    protected function getSnippetPath($flatResult, $snippetId, $currentPath = '')
    {
        foreach ($flatResult as $snippet) {
            if ($snippet['surrogateId'] === $snippetId) {
                if ($currentPath === '') {
                    $currentPath = $snippet['name'];
                }
                else {
                    $currentPath = $snippet['name'] . '_' . $currentPath;
                }
                if ($snippet['parentId']) {
                    $currentPath = $this->getSnippetPath($flatResult, $snippet['parentId'], $currentPath);
                    return $currentPath;
                }
            }
        }
        return $currentPath;
    }

    /**
     * @param $flatResult
     * @param $snippetId
     * @return bool
     */
    protected function hasChildren($flatResult, $snippetId)
    {
        foreach ($flatResult as $snippet) {
            if ($snippet['parentId'] === $snippetId) {
                return true;
            }
        }
        return false;
    }
}
