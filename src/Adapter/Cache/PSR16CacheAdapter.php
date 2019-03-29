<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\Cache;


use Psr\SimpleCache\CacheInterface;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Adapter\AdapterInterface;
use Secretary\Adapter\Secret;
use Secretary\Helper\ArrayHelper;

/**
 * Class PSR16CacheAdapter
 *
 * @package Secretary\Adapter\Cache
 */
final class PSR16CacheAdapter extends AbstractAdapter
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * CacheAdapter constructor.
     *
     * @param AdapterInterface $adapter
     * @param CacheInterface   $cache
     */
    public function __construct(AdapterInterface $adapter, CacheInterface $cache)
    {
        $this->adapter = $adapter;
        $this->cache   = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        [$ttl] = ArrayHelper::remove($options, 'ttl');

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
        [$ttl] = ArrayHelper::remove($options, 'ttl');

        $this->adapter->putSecret($key, $value, $options);
        if ($this->cache->has($key) || $ttl === 0) {
            $this->cache->delete($key);

            return;
        }

        $this->cache->set($key, $value, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecret(string $key, ?array $options = []): void
    {
        $this->adapter->deleteSecret($key, $options);
        $this->cache->delete($key);
    }

    /**
     * @param string   $key
     * @param callable $callback
     * @param int|null $ttl
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function memoize(string $key, callable $callback, int $ttl = null)
    {
        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $cachedValue = $callback();
        $this->cache->set($key, $cachedValue, $ttl);

        return $cachedValue;
    }
}
