<?php

namespace Apto\Base\Application\Backend\Commands\ContentSnippet;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddContentSnippet implements CommandInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var array
     */
    protected $content;

    /**
     * @var string
     */
    protected $parent;

    /**
     * @var bool
     */
    protected $html;

    /**
     * AddContentSnippet constructor.
     * @param string $name
     * @param bool|null $active
     * @param array|null $content
     * @param string|null $parent
     * @param bool $html
     */
    public function __construct(
        string $name,
        bool $active = null,
        array $content = null,
        string $parent = null,
        bool $html = null
    ) {
        $this->name = $name;
        $this->active = $active !== null ? $active : true;
        $this->content = $content;
        $this->parent = $parent;
        $this->html =  $html ? $html : false;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return array|null
     */
    public function getContent(): ?array
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @return bool
     */
    public function getHtml(): bool
    {
        return $this->html;
    }
}