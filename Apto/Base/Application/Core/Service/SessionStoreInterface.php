<?php

namespace Apto\Base\Application\Core\Service;

interface SessionStoreInterface
{
    /**
     * @return string|null
     */
    public function getFrontendUserId(): ?string;

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $name, $default);

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function set(string $name, $value);
}