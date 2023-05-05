<?php
namespace Apto\Base\Infrastructure\AptoBaseBundle\Template;

interface BackendTemplateInterface
{
    /**
     * @return array
     */
    public function getRoutes(): array;

    /**
     * @return array
     */
    public function getMainMenuEntries(): array;

    /**
     * @return array
     */
    public function getTemplates(): array;
}

