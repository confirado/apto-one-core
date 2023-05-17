<?php

namespace Apto\Plugins\ImageUpload\Application\Backend\Commands;

use Apto\Base\Application\Core\CommandInterface;

class UpdateCanvas extends AbstractAddCanvas implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     * @param string $identifier
     * @param array $imageSettings
     * @param array $textSettings
     * @param array $areaSettings
     * @param array $priceSettings
     */
    public function __construct(string $id, string $identifier, array $imageSettings, array $textSettings, array $areaSettings, array $priceSettings)
    {
        parent::__construct($identifier, $imageSettings, $textSettings, $areaSettings, $priceSettings);
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
