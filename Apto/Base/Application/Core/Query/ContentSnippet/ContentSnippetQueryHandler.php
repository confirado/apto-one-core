<?php

namespace Apto\Base\Application\Core\Query\ContentSnippet;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Catalog\Application\Core\Query\Shop\ShopFinder;
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
     * @var ShopFinder
     */
    private $shopFinder;

    /**
     * @param ContentSnippetFinder $contentSnippetFinder
     * @param RequestStore $requestStore
     * @param ShopFinder $shopFinder
     */
    public function __construct(
        ContentSnippetFinder $contentSnippetFinder,
        RequestStore $requestStore,
        ShopFinder $shopFinder
    ) {
        $this->contentSnippetFinder = $contentSnippetFinder;
        $this->requestStore = $requestStore;
        $this->shopFinder = $shopFinder;
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

        $host = $this->requestStore->getHttpHost();

        $tree = $this->contentSnippetFinder->getTree($query->getFrontend(), $host, $query->getFrontendIndexed());


        if ($treeType !== 'Backend') {
            $ignoredSnippetPrefixes = $this->findAllSnippetPrefixes();

            $currentSnippetPrefix = $this->findSnippetPrefixForHost($host);
            if (isset($currentSnippetPrefix) && !empty($currentSnippetPrefix)) {
                $ignoredSnippetPrefixes = array_diff(
                    $ignoredSnippetPrefixes,
                    [$currentSnippetPrefix]
                );
            }

            if (isset($ignoredSnippetPrefixes) && is_array($ignoredSnippetPrefixes) && count($ignoredSnippetPrefixes) > 0) {
                foreach ($ignoredSnippetPrefixes as $ignoredSnippetPrefix) {
                    $this->removeContentSnippetPrefixes($tree, $currentSnippetPrefix, $ignoredSnippetPrefix);
                }
            }
        }

        AptoCacheService::setItem('ContentSnippetTree-' . $treeType, $tree);
        return $tree;
    }


    private function removeContentSnippetPrefixes(array &$node, string $currentSnippetPrefix, string $ignoredSnippetPrefix): void {
        $wasList = array_is_list($node);

        foreach ($node as $key => &$child) {
            if (!is_array($child)) {
                continue;
            }

            if (isset($child['name']) && is_string($child['name'])) {
                if ($currentSnippetPrefix !== '' && str_starts_with($child['name'], $currentSnippetPrefix . '_')) {
                    $child['name'] = substr($child['name'], strlen($currentSnippetPrefix . '_'));
                }
                elseif (str_starts_with($child['name'], $ignoredSnippetPrefix . '_')) {
                    unset($node[$key]);
                    continue;
                }
            }

            $this->removeContentSnippetPrefixes($child, $currentSnippetPrefix, $ignoredSnippetPrefix);
        }

        unset($child);

        if ($wasList) {
            $node = array_values($node);
        }
    }


    private function findShopCustomProperties($shop) {
        $customProperties = $this->shopFinder->findCustomProperties($shop['id']);

        if (isset($customProperties) && isset($customProperties['customProperties'])) {
            return $customProperties['customProperties'];
        }

        return null;
    }

    private function findSnippetPrefixForShop($shop) {
        $snippetPrefix = '';

        if (!isset($shop)) {
            return $snippetPrefix;
        }


        $customProperties = $this->findShopCustomProperties($shop);

        foreach ($customProperties as $customProperty) {
            if ($customProperty['key'] === 'snippetPrefix') {
                $snippetPrefix = $customProperty['value'];
            }
        }

        return $snippetPrefix;
    }

    private function findSnippetPrefixForHost($host) {
        $shop = $this->shopFinder->findByDomain($host);
        return $this->findSnippetPrefixForShop($shop);
    }

    private function findAllSnippetPrefixes() {
        $snippetPrefixes = [];

        $shops = $this->shopFinder->findShops();

        if (isset($shops) && isset($shops['data'])) {
            $shops = $shops['data'];

            foreach ($shops as $shop) {
                $snippetPrefix = $this->findSnippetPrefixForShop($shop);
                if (isset($snippetPrefix) && !empty($snippetPrefix)) {
                    $snippetPrefixes[] = $snippetPrefix;
                }
            }
        }

        return $snippetPrefixes;
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
