<?php

namespace Apto\Base\Domain\Core\Model\ContentSnippet;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;

class ContentSnippet extends AptoAggregate
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var AptoTranslatedValue|null
     */
    protected $content;

    /**
     * @var ContentSnippet|null
     */
    protected $parent;

    /**
     * @var integer
     */
    protected $parentId;

    /**
     * @var boolean
     */
    protected $html;

    /**
     * ContentSnippet constructor.
     * @param AptoUuid $id
     * @param string $name
     * @param bool $active
     * @param AptoTranslatedValue|null $content
     * @param ContentSnippet|null $parent
     * @param bool $html
     * @throws ContentSnippetNameException
     * @throws ContentSnippetParentException
     */
    public function __construct(
        AptoUuid $id,
        string $name,
        bool $active,
        ?AptoTranslatedValue $content = null,
        ?ContentSnippet $parent = null,
        bool $html = false
    ) {
        parent::__construct($id);
        $this->setName($name);
        $this->setActive($active);
        $this->setContent($content);
        $this->setParent($parent);
        $this->setHtml($html);

        $this->publish(
            new ContentSnippetAdded(
                $id,
                $name
            )
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ContentSnippet
     * @throws ContentSnippetNameException
     */
    public function setName(string $name): ContentSnippet
    {
        // assert valid name
        if ('content' === $name) {
            throw new ContentSnippetNameException('Name \'content\' is not a valid ContentSnippet name.');
        }

        // return if name not changed
        if ($this->name === $name) {
            return $this;
        }

        // change name
        $this->name = $name;
        $this->publish(
            new ContentSnippetNameChanged(
                $this->getId(),
                $name
            )
        );
        return $this;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return ContentSnippet
     */
    public function setActive(bool $active): ContentSnippet
    {
        // return if active not changed
        if ($this->active === $active) {
            return $this;
        }

        // change active
        $this->active = $active;
        $this->publish(
            new ContentSnippetActiveChanged(
                $this->getId(),
                $active
            )
        );
        return $this;
    }

    /**
     * @return AptoTranslatedValue|null
     */
    public function getContent(): ?AptoTranslatedValue
    {
        return $this->content;
    }

    /**
     * @param AptoTranslatedValue|null $content
     * @return ContentSnippet
     */
    public function setContent(?AptoTranslatedValue $content): ContentSnippet
    {
        // case parent to set equals null
        if (null === $content) {
            if (null === $this->content) {
                return $this;
            }

            $this->content = $content;
            $this->publish(
                new ContentSnippetContentChanged(
                    $this->getId(),
                    $content
                )
            );
            return $this;
        }

        // case content to set is of type AptoTranslatedValue
        if (null !== $this->content && $this->content->equals($content)) {
            return $this;
        }

        // change content
        $this->content = $content;
        $this->publish(
            new ContentSnippetContentChanged(
                $this->getId(),
                $content
            )
        );
        return $this;
    }

    /**
     * @return ContentSnippet|null
     */
    public function getParent(): ?ContentSnippet
    {
        return $this->parent;
    }

    /**
     * @param ContentSnippet|null $parent
     * @return ContentSnippet
     * @throws ContentSnippetParentException
     */
    public function setParent(?ContentSnippet $parent = null): ContentSnippet
    {
        // case parent to set equals null
        if (null === $parent) {
            if (null === $this->parent) {
                return $this;
            }

            $this->parent = $parent;
            $this->publish(
                new ContentSnippetParentChanged(
                    $this->getId(),
                    $parent
                )
            );
            return $this;
        }

        // case parent to set is of type ContentSnippet
        // parent equals current parent
        if (null !== $this->parent && $this->parent->getId()->getId() === $parent->getId()->getId()) {
            return $this;
        }

        // assert valid content snippet
        if ($parent->getId() === $this->getId()) {
            throw new ContentSnippetParentException('Parent cannot be set to self!');
        }

        if (sizeof($parent->getContent()->__toArray()) > 0) {
            forEach ($parent->getContent()->__toArray() as $iso => $value) {
                if ($value > 0) {
                    throw new ContentSnippetParentException('Parent ContentSnippet is not allowed to have Values!');
                }
            }
        }

        // change parent
        $this->parent = $parent;
        $this->publish(
            new ContentSnippetParentChanged(
                $this->getId(),
                $parent
            )
        );
        return $this;
    }

    /**
     * @return bool
     */
    public function getHtml(): bool
    {
        return $this->html;
    }

    /**
     * @param bool $html
     * @return ContentSnippet
     */
    public function setHtml(bool $html): ContentSnippet
    {
        // return if html not changed
        if ($this->html === $html) {
            return $this;
        }

        // change html
        $this->html = $html;
        $this->publish(
            new ContentSnippetHtmlChanged(
                $this->getId(),
                $html
            )
        );
        return $this;
    }
}