<?php

namespace Apto\Base\Application\Backend\Commands\Settings;

class UpdateSettings extends AbstractAddSettings
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @param string $id
     * @param string $colorPrimary
     * @param string $colorPrimaryHover
     * @param string $colorAccent
     * @param string $colorBackgroundHeader
     * @param string $colorBackgroundFooter
     * @param string $colorTitle
     * @param string $colorText
     */
    public function __construct(
        string $id,
        string $colorPrimary,
        string $colorPrimaryHover,
        string $colorAccent,
        string $colorBackgroundHeader,
        string $colorBackgroundFooter,
        string $colorTitle,
        string $colorText
    ) {
        parent::__construct(
            $colorPrimary,
            $colorPrimaryHover,
            $colorAccent,
            $colorBackgroundHeader,
            $colorBackgroundFooter,
            $colorTitle,
            $colorText
        );
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
