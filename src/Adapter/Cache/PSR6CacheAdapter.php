<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\Cache;


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
    public function getSecret(string $key, array $options): Secret
    {
        [$ttl] = ArrayHelper::remove($options, 'ttl');

        return $this->memoize(
            json_encode($options),
            function () use ($key, $options) {
                return $this->adapter->getSecret($key, $options);
            },
            $ttl
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSecrets(array $options): array
    {
        [$ttl] = ArrayHelper::remove($options, 'ttl');

        return $this->memoize(
            json_encode($options),
            function () use ($options) {
                return $this->adapter->getSecrets($options);
            },
            $ttl
        );
    }

    /**
     * {@inheritdoc}
     */
    public function putSecret(string $key, string $value, array $options): void
    {
        $this->adapter->putSecret($key, $value, $options);
        $this->cache->clear();
    }

    /**
     * @param array $options
     */
    public function putSecrets(array $options): void
    {
        $this->adapter->putSecrets($options);
        $this->cache->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecret(string $key, array $options): void
    {
        $this->adapter->deleteSecret($key, $options);
        $this->cache->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecrets(array $options): void
    {
        $this->adapter->deleteSecrets($options);
        $this->cache->clear();
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
        $item = $this->cache->getItem($key);
        if ($item !== null) {
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
