<?php

namespace Apto\Base\Application\Core\Service;

interface RequestStore
{
    /**
     * @return string
     */
    public function getSchemeAndHttpHost(): string;

    /**
     * @return string
     */
    public function getHttpHost(): string;

    /**
     * @return string
     */
    public function getLocale(): string;

    /**
     * @return string
     */
    public function getDefaultLocale(): string;

    /**
     * @return string
     */
    public function getBasePath(): string;
}
