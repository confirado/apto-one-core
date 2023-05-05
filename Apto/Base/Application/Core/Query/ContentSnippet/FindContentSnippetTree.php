<?php

namespace Apto\Base\Application\Core\Query\ContentSnippet;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindContentSnippetTree implements PublicQueryInterface
{
    /**
     * @var bool
     */
    protected $frontend;

    /**
     * @var bool
     */
    protected $frontendIndexed;

    /**
     * @param bool $frontend
     * @param bool $frontendIndexed
     */
    public function __construct(bool $frontend = false, bool $frontendIndexed = false)
    {
        $this->frontend = $frontend;
        $this->frontendIndexed = $frontendIndexed;
    }

    /**
     * @return bool
     */
    public function getFrontend(): bool
    {
        return $this->frontend;
    }

    /**
     * @return bool
     */
    public function getFrontendIndexed(): bool
    {
        return $this->frontendIndexed;
    }
}
