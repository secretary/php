<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\Cache\PSR16Cache;

use Psr\SimpleCache\CacheInterface;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Adapter\AdapterInterface;
use Secretary\Helper\ArrayHelper;
use Secretary\Secret;

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
        ['ttl' => $ttl] = ArrayHelper::remove($options, 'ttl');

        if ($this->cache->has(sha1($key))) {
            [$value, $metadata] = $this->cache->get(sha1($key));

            return new Secret($key, $value, $metadata);
        }

        $secret = $this->adapter->getSecret($key, $options);
        $this->cache->set(sha1($key), [$secret->getValue(), $secret->getMetadata()], $ttl);

        return $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        ['ttl' => $ttl] = ArrayHelper::remove($options, 'ttl');

        $this->adapter->putSecret($secret, $options);
        if ($this->cache->has(sha1($secret->getKey())) || $ttl === 0) {
            $this->cache->delete(sha1($secret->getKey()));

            return $secret;
        }

        $this->cache->set(sha1($secret->getKey()), [$secret->getValue(), $secret->getMetadata()], $ttl);

        return $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecret(Secret $secret, ?array $options = []): void
    {
        $this->deleteSecretByKey($secret->getKey(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecretByKey(string $key, ?array $options = []): void
    {
        $this->adapter->deleteSecret($key, $options);
        $this->cache->delete(sha1($key));
    }
}
