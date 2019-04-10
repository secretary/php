<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\Cache\PSR6Cache;


use Psr\Cache\CacheItemPoolInterface;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Adapter\AdapterInterface;
use Secretary\Adapter\Secret;
use Secretary\Helper\ArrayHelper;

/**
 * Class PSR6CacheAdapter
 *
 * @package Secretary\Adapter\Cache
 */
final class PSR6CacheAdapter extends AbstractAdapter
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * CacheAdapter constructor.
     *
     * @param AdapterInterface       $adapter
     * @param CacheItemPoolInterface $cache
     */
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

        return $this->memoize(
            $key,
            function () use ($key, $options) {
                return $this->adapter->getSecret($key, $options);
            },
            $ttl
        );
    }

    /**
     * {@inheritdoc}
     */
    public function putSecret(string $key, $value, ?array $options = []): void
    {
        ['ttl' => $ttl] = ArrayHelper::remove($options, 'ttl');

        $this->adapter->putSecret($key, $value, $options);
        if ($this->cache->hasItem(sha1($key)) || $ttl === 0) {
            $this->cache->deleteItem(sha1($key));

            return;
        }

        $item = $this->cache->getItem(sha1($key));
        $item->set($value);
        if (!empty($ttl)) {
            $item->expiresAfter($ttl);
        }
        $this->cache->save($item);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecret(string $key, ?array $options = []): void
    {
        $this->adapter->deleteSecret($key, $options);
        if ($this->cache->hasItem(sha1($key))) {
            $this->cache->deleteItem(sha1($key));
        }
    }

    /**
     * @param string   $key
     * @param callable $callback
     * @param int|null $ttl
     *
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function memoize(string $key, callable $callback, int $ttl = null)
    {
        $item = $this->cache->getItem(sha1($key));
        if ($item !== null && $item->isHit()) {
            return $item->get();
        }

        $cachedValue = $callback();
        $item->set($cachedValue);
        if ($ttl !== null) {
            $item->expiresAfter($ttl);
        }
        $this->cache->save($item);

        return $cachedValue;
    }
}
