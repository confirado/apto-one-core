<?php

namespace Apto\Base\Application\Core\Service\AptoCache;

use Symfony\Component\Cache\Adapter\ApcuAdapter;
use \Psr\Cache\InvalidArgumentException;
use \Symfony\Component\Cache\Exception\CacheException;

class AptoCacheService
{
    const DEFAULT_TTL = 24*60*60;
    /**
     * @param string $key
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function getItem(string $key)
    {
        $apcuEnabled = $_ENV['APCU_ENABLED'] ?? $_SERVER['APCU_ENABLED'] ?? false;
        if (!$apcuEnabled) {
            return null;
        }

        self::applyPrefix($key);

        $cache = new ApcuAdapter();

        if($cache->hasItem($key)) {
            return $cache->getItem($key)->get();
        }
        return null;
    }

    /**
     * @param string $key
     * @param $object
     * @param int $ttl
     * @return void
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public static function setItem(string $key, $object, int $ttl = self::DEFAULT_TTL)
    {
        $apcuEnabled = $_ENV['APCU_ENABLED'] ?? $_SERVER['APCU_ENABLED'] ?? false;
        if (!$apcuEnabled) {
            return;
        }
        self::applyPrefix($key);
        $cache = new ApcuAdapter('', $ttl);
        $item = $cache->getItem($key);
        $cache->save($item->set($object));
    }

    /**
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    public static function deleteItem(string $key)
    {
        $apcuEnabled = $_ENV['APCU_ENABLED'] ?? $_SERVER['APCU_ENABLED'] ?? false;
        if (!$apcuEnabled) {
            return;
        }
        self::applyPrefix($key);
        $cache = new ApcuAdapter();

        if($cache->hasItem($key)) {
            $cache->delete($key);
        }
    }

    /**
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function hasItem(string $key): bool
    {
        $apcuEnabled = $_ENV['APCU_ENABLED'] ?? $_SERVER['APCU_ENABLED'] ?? false;
        if (!$apcuEnabled) {
            return false;
        }
        self::applyPrefix($key);
        $cache = new ApcuAdapter();

        return $cache->hasItem($key);
    }

    /**
     * @param string $prefix
     * @return void
     */
    public static function clearCache(string $prefix = '')
    {
        $apcuEnabled = $_ENV['APCU_ENABLED'] ?? $_SERVER['APCU_ENABLED'] ?? false;
        if (!$apcuEnabled) {
            return;
        }
        $cache = new ApcuAdapter();
        self::applyPrefix($prefix);
        $cache->clear($prefix);
    }

    /**
     * @param string $key
     * @return void
     */
    private static function applyPrefix(string &$key)
    {
        // a prefix is needed on shared environments such as mein.confirado.net or live+staging running on the same php instance
        $apcuPrefix = $_ENV['APCU_PREFIX'] ?? $_SERVER['APCU_PREFIX'] ?? null;
        if ($apcuPrefix) {
            $key = $apcuPrefix . '-' . $key;
        }
    }
}