<?php

namespace Apto\Base\Application\Backend\Commands\Settings;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddSettings implements CommandInterface
{
    /**
     * @var string
     */
    private string $primaryColor;

    /**
     * @var string
     */
    private string $secondaryColor;

    /**
     * @var string
     */
    private string $backgroundColorHeader;

    /**
     * @var string
     */
    private string $fontColorHeader;

    /**
     * @var string
     */
    private string $backgroundColorFooter;

    /**
     * @var string
     */
    private string $fontColorFooter;

    /**
     * @param string $primaryColor
     * @param string $secondaryColor
     * @param string $backgroundColorHeader
     * @param string $fontColorHeader
     * @param string $backgroundColorFooter
     * @param string $fontColorFooter
     */
    public function __construct(
        string $primaryColor,
        string $secondaryColor,
        string $backgroundColorHeader,
        string $fontColorHeader,
        string $backgroundColorFooter,
        string $fontColorFooter
    ) {
        $this->primaryColor = $primaryColor;
        $this->secondaryColor = $secondaryColor;
        $this->backgroundColorHeader = $backgroundColorHeader;
        $this->fontColorHeader = $fontColorHeader;
        $this->backgroundColorFooter = $backgroundColorFooter;
        $this->fontColorFooter = $fontColorFooter;
    }

    /**
     * @return string
     */
    public function getPrimaryColor(): string
    {
        return $this->primaryColor;
    }

    /**
     * @return string
     */
    public function getSecondaryColor(): string
    {
        return $this->secondaryColor;
    }

    /**
     * @return string
     */
    public function getBackgroundColorHeader(): string
    {
        return $this->backgroundColorHeader;
    }

    /**
     * @return string
     */
    public function getFontColorHeader(): string
    {
        return $this->fontColorHeader;
    }

    /**
     * @return string
     */
    public function getBackgroundColorFooter(): string
    {
        return $this->backgroundColorFooter;
    }

    /**
     * @return string
     */
    public function getFontColorFooter(): string
    {
        return $this->fontColorFooter;
    }
}
