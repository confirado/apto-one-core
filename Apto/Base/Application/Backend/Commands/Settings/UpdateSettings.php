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
     * @param string $primaryColor
     * @param string $secondaryColor
     * @param string $backgroundColorHeader
     * @param string $fontColorHeader
     * @param string $backgroundColorFooter
     * @param string $fontColorFooter
     */
    public function __construct(
        string $id,
        string $primaryColor,
        string $secondaryColor,
        string $backgroundColorHeader,
        string $fontColorHeader,
        string $backgroundColorFooter,
        string $fontColorFooter
    ) {
        parent::__construct(
            $primaryColor,
            $secondaryColor,
            $backgroundColorHeader,
            $fontColorHeader,
            $backgroundColorFooter,
            $fontColorFooter
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
