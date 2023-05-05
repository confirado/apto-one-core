<?php

namespace Apto\Base\Application\Backend\Commands\Language;

class UpdateLanguage extends AbstractAddLanguage
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateLanguage constructor.
     * @param string $id
     * @param array $name
     * @param string $isocode
     */
    public function __construct(string $id, array $name, string $isocode)
    {
        parent::__construct($name, $isocode);
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