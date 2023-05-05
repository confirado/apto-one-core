<?php

namespace Apto\Base\Application\Core\Query\ContentSnippet;

use Apto\Base\Application\Core\Query\AptoFinder;

interface ContentSnippetFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $searchString
     * @return array
     */
    public function findContentSnippets(string $searchString = ''): array;

    /**
     * @param string $searchString
     * @return array
     */
    public function findChildren(string $searchString = ''): array;

    /**
     * @param bool $frontend
     * @param string $domain
     * @param bool $indexedFrontendTree
     * @return array
     */
    public function getTree(bool $frontend, string $domain, bool $indexedFrontendTree = false): array;

    /**
     * @return array
     */
    public function getExportItems(): array;
}
