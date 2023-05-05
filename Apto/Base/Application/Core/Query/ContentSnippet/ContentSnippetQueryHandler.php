<?php

namespace Apto\Base\Application\Core\Query\ContentSnippet;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Application\Core\Service\RequestStore;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Exception\CacheException;

class ContentSnippetQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ContentSnippetFinder
     */
    private $contentSnippetFinder;

    /**
     * @var RequestStore
     */
    private $requestStore;

    /**
     * @param ContentSnippetFinder $contentSnippetFinder
     * @param RequestStore $requestStore
     */
    public function __construct(
        ContentSnippetFinder $contentSnippetFinder,
        RequestStore $requestStore
    ) {
        $this->contentSnippetFinder = $contentSnippetFinder;
        $this->requestStore = $requestStore;
    }

    /**
     * @param FindContentSnippet $query
     * @return array
     */
    public function handleFindContentSnippet(FindContentSnippet $query)
    {
        return $this->contentSnippetFinder->findById($query->getId());
    }

    /**
     * @param FindContentSnippetTree $query
     * @return array|mixed
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function handleFindContentSnippetTree (FindContentSnippetTree $query)
    {
        $treeType = $query->getFrontend() ? 'Frontend' : 'Backend';
        $treeType .= $query->getFrontendIndexed() ? 'Indexed' : '';
        $tree = AptoCacheService::getItem('ContentSnippetTree-' . $treeType);
        if ($tree) {
            return $tree;
        }
        $tree = $this->contentSnippetFinder->getTree($query->getFrontend(), $this->requestStore->getHttpHost(), $query->getFrontendIndexed());
        AptoCacheService::setItem('ContentSnippetTree-' . $treeType, $tree);
        return $tree;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindContentSnippet::class => [
            'method' => 'handleFindContentSnippet',
            'bus' => 'query_bus'
        ];

        yield FindContentSnippetTree::class => [
            'method' => 'handleFindContentSnippetTree',
            'bus' => 'query_bus'
        ];
    }
}
