<?php

namespace Apto\Base\Application\Backend\Commands\ContentSnippet;

class UpdateContentSnippet extends AbstractAddContentSnippet
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $content;

    /**
     * @var string
     */
    protected $parent;

    /**
     * UpdateContentSnippet constructor.
     * @param string $id
     * @param string $name
     * @param bool $active
     * @param array|null $content
     * @param string|null $parent
     * @param bool $html
     */
    public function __construct(
        string $id,
        string $name,
        bool $active,
        array $content = null,
        string $parent = null,
        bool $html = null
    ) {
        parent::__construct($name, $active, $content, $parent, $html);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}