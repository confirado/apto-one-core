<?php

namespace Apto\Base\Application\Backend\Commands\Settings;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddSettings implements CommandInterface
{
    /**
     * @var string
     */
    private string $colorPrimary;

    /**
     * @var string
     */
    private string $colorPrimaryHover;

    /**
     * @var string
     */
    private string $colorAccent;

    /**
     * @var string
     */
    private string $colorBackgroundHeader;

    /**
     * @var string
     */
    private string $colorBackgroundFooter;

    /**
     * @var string
     */
    private string $colorTitle;

    /**
     * @var string
     */
    private string $colorText;

    /**
     * @param string $colorPrimary
     * @param string $colorPrimaryHover
     * @param string $colorAccent
     * @param string $colorBackgroundHeader
     * @param string $colorBackgroundFooter
     * @param string $colorTitle
     * @param string $colorText
     */
    public function __construct(
        string $colorPrimary,
        string $colorPrimaryHover,
        string $colorAccent,
        string $colorBackgroundHeader,
        string $colorBackgroundFooter,
        string $colorTitle,
        string $colorText
    ) {
        $this->colorPrimary = $colorPrimary;
        $this->colorPrimaryHover = $colorPrimaryHover;
        $this->colorAccent = $colorAccent;
        $this->colorBackgroundHeader = $colorBackgroundHeader;
        $this->colorBackgroundFooter = $colorBackgroundFooter;
        $this->colorTitle = $colorTitle;
        $this->colorText = $colorText;
    }

    /**
     * @return string
     */
    public function getColorPrimary(): string
    {
        return $this->colorPrimary;
    }

    /**
     * @return string
     */
    public function getColorPrimaryHover(): string
    {
        return $this->colorPrimaryHover;
    }

    /**
     * @return string
     */
    public function getColorAccent(): string
    {
        return $this->colorAccent;
    }

    /**
     * @return string
     */
    public function getColorBackgroundHeader(): string
    {
        return $this->colorBackgroundHeader;
    }

    /**
     * @return string
     */
    public function getColorBackgroundFooter(): string
    {
        return $this->colorBackgroundFooter;
    }

    /**
     * @return string
     */
    public function getColorTitle(): string
    {
        return $this->colorTitle;
    }

    /**
     * @return string
     */
    public function getColorText(): string
    {
        return $this->colorText;
    }
}
