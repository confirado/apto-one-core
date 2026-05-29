<?php

namespace Apto\Base\Domain\Core\Model\Settings;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class Settings extends AptoAggregate
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
     * @param AptoUuid $id
     * @param string $colorPrimary
     * @param string $colorPrimaryHover
     * @param string $colorAccent
     * @param string $colorBackgroundHeader
     * @param string $colorBackgroundFooter
     * @param string $colorTitle
     * @param string $colorText
     */
    public function __construct(
        AptoUuid $id,
        string $colorPrimary,
        string $colorPrimaryHover,
        string $colorAccent,
        string $colorBackgroundHeader,
        string $colorBackgroundFooter,
        string $colorTitle,
        string $colorText
    ) {
        parent::__construct($id);
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
     * @param $colorPrimary
     * @return $this
     */
    public function setColorPrimary($colorPrimary): Settings
    {
        $this->colorPrimary = $colorPrimary;
        return $this;
    }

    /**
     * @return string
     */
    public function getColorPrimaryHover(): string
    {
        return $this->colorPrimaryHover;
    }

    /**
     * @param $colorPrimaryHover
     * @return $this
     */
    public function setColorPrimaryHover($colorPrimaryHover): Settings
    {
        $this->colorPrimaryHover = $colorPrimaryHover;
        return $this;
    }

    /**
     * @return string
     */
    public function getColorAccent(): string
    {
        return $this->colorAccent;
    }

    /**
     * @param $colorAccent
     * @return $this
     */
    public function setColorAccent($colorAccent): Settings
    {
        $this->colorAccent = $colorAccent;
        return $this;
    }

    /**
     * @return string
     */
    public function getColorBackgroundHeader(): string
    {
        return $this->colorBackgroundHeader;
    }

    /**
     * @param $colorBackgroundHeader
     * @return $this
     */
    public function setColorBackgroundHeader($colorBackgroundHeader): Settings
    {
        $this->colorBackgroundHeader = $colorBackgroundHeader;
        return $this;
    }

    /**
     * @return string
     */
    public function getColorBackgroundFooter(): string
    {
        return $this->colorBackgroundFooter;
    }

    /**
     * @param $colorBackgroundFooter
     * @return $this
     */
    public function setColorBackgroundFooter($colorBackgroundFooter): Settings
    {
        $this->colorBackgroundFooter = $colorBackgroundFooter;
        return $this;
    }

    /**
     * @return string
     */
    public function getColorTitle(): string
    {
        return $this->colorTitle;
    }

    /**
     * @param $colorTitle
     * @return $this
     */
    public function setColorTitle($colorTitle): Settings
    {
        $this->colorTitle = $colorTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getColorText(): string
    {
        return $this->colorText;
    }

    /**
     * @param $colorText
     * @return $this
     */
    public function setColorText($colorText): Settings
    {
        $this->colorText = $colorText;
        return $this;
    }
}
