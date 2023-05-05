<?php

namespace Apto\Base\Application\Core\Service;

interface TemplateRendererInterface
{
    /**
     * @param string $template
     * @param array $context
     * @return string
     */
    public function render(string $template, array $context = []): string;

    /**
     * @param string $template
     * @return bool
     */
    public function templateExists(string $template): bool;
}