<?php

namespace Apto\Base\Domain\Core\Model\Settings;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class Settings extends AptoAggregate
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
     * @param AptoUuid $id
     * @param string $primaryColor
     * @param string $secondaryColor
     * @param string $backgroundColorHeader
     * @param string $fontColorHeader
     * @param string $backgroundColorFooter
     * @param string $fontColorFooter
     */
    public function __construct(
        AptoUuid $id,
        string $primaryColor,
        string $secondaryColor,
        string $backgroundColorHeader,
        string $fontColorHeader,
        string $backgroundColorFooter,
        string $fontColorFooter
    ) {
        parent::__construct($id);
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
     * @param $primaryColor
     * @return $this
     */
    public function setPrimaryColor($primaryColor): Settings
    {
        $this->primaryColor = $primaryColor;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecondaryColor(): string
    {
        return $this->secondaryColor;
    }

    /**
     * @param $secondaryColor
     * @return $this
     */
    public function setSecondaryColor($secondaryColor): Settings
    {
        $this->secondaryColor = $secondaryColor;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundColorHeader(): string
    {
        return $this->backgroundColorHeader;
    }

    /**
     * @param $backgroundColorHeader
     * @return $this
     */
    public function setBackgroundColorHeader($backgroundColorHeader): Settings
    {
        $this->backgroundColorHeader = $backgroundColorHeader;
        return $this;
    }

    /**
     * @return string
     */
    public function getFontColorHeader(): string
    {
        return $this->fontColorHeader;
    }

    /**
     * @param $fontColorHeader
     * @return $this
     */
    public function setFontColorHeader($fontColorHeader): Settings
    {
        $this->fontColorHeader = $fontColorHeader;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundColorFooter(): string
    {
        return $this->backgroundColorFooter;
    }

    /**
     * @param $backgroundColorFooter
     * @return $this
     */
    public function setBackgroundColorFooter($backgroundColorFooter): Settings
    {
        $this->backgroundColorFooter = $backgroundColorFooter;
        return $this;
    }

    /**
     * @return string
     */
    public function getFontColorFooter(): string
    {
        return $this->fontColorFooter;
    }

    /**
     * @param $fontColorFooter
     * @return $this
     */
    public function setFontColorFooter($fontColorFooter): Settings
    {
        $this->fontColorFooter = $fontColorFooter;
        return $this;
    }
}
