<?php

namespace Apto\Base\Domain\Core\Model\ContentSnippet;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface ContentSnippetRepository extends AptoRepository
{
    /**
     * @param ContentSnippet $model
     */
    public function update(ContentSnippet $model);

    /**
     * @param ContentSnippet $model
     */
    public function add(ContentSnippet $model);

    /**
     * @param ContentSnippet $model
     */
    public function remove(ContentSnippet $model);

    /**
     * @param string $id
     * @return ContentSnippet|null
     */
    public function findById($id);

    /**
     * @param string $name
     * @return ContentSnippet|null
     */
    public function findOneByName(string $name);

    /**
     * @param string $parentId
     * @return bool
     */
    public function hasChildren(string $parentId);

    /**
     * @return void
     */
    public function invalidateCache();

}