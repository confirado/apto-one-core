<?php

namespace Apto\Base\Application\Core\Service;

interface TemplateMailerInterface
{
    /**
     * @param array $payload
     * @return void
     */
    public function send(array $payload);

    /**
     * @param string $template
     * @return bool
     */
    public function templateExists(string $template): bool;
}