<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter\Cache\PSR6Cache;

use Psr\Cache\CacheItemPoolInterface;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Adapter\AdapterInterface;
use Secretary\Helper\ArrayHelper;
use Secretary\Secret;

/**
 * Class PSR6CacheAdapter.
 *
 * @package Secretary\Adapter\Cache
 */
final class PSR6CacheAdapter extends AbstractAdapter
{
    private AdapterInterface $adapter;

    private CacheItemPoolInterface $cache;

    public function __construct(AdapterInterface $adapter, CacheItemPoolInterface $cache)
    {
        $this->adapter = $adapter;
        $this->cache   = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        ['ttl' => $ttl] = ArrayHelper::remove($options, 'ttl');

        $item = $this->cache->getItem(sha1($key));

        if ($item->isHit()) {
            [$value, $metadata] = json_decode($item->get(), true);

            return new Secret($key, $value, $metadata);
        }

        $secret = $this->adapter->getSecret($key, $options);
        $item->set(json_encode([$secret->getValue(), $secret->getMetadata()]));

        if ($ttl !== null) {
            $item->expiresAfter($ttl);
        }
        $this->cache->save($item);

        return $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        ['ttl' => $ttl] = ArrayHelper::remove($options, 'ttl');

        $this->adapter->putSecret($secret, $options);

        if ($this->cache->hasItem(sha1($secret->getKey())) || $ttl === 0) {
            $this->cache->deleteItem(sha1($secret->getKey()));

            return $secret;
        }

        $item = $this->cache->getItem(sha1($secret->getKey()));
        $item->set(json_encode([$secret->getValue(), $secret->getMetadata()]));

        if (!empty($ttl)) {
            $item->expiresAfter($ttl);
        }
        $this->cache->save($item);

        return $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecret(Secret $secret, ?array $options = []): void
    {
        $this->deleteSecretByKey($secret->getKey(), $options);
    }

    public function deleteSecretByKey(string $key, ?array $options = []): void
    {
        $this->adapter->deleteSecret($this->adapter->getSecret($key, $options), $options);

        if ($this->cache->hasItem(sha1($key))) {
            $this->cache->deleteItem(sha1($key));
        }
    }
}
