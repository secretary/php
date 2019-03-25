<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      ${YEAR}
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Secretary\Adapter\AdapterInterface;
use Secretary\Adapter\Secret;
use Secretary\Configuration\ManagerConfiguration;
use Secretary\Helper\ArrayHelper;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class Manager
 *
 * @package Secretary
 */
class Manager
{
	/**
	 * @var AdapterInterface
	 */
	private $adapter;

	/**
	 * @var array
	 */
	private $config;

	/**
	 * Manager constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$processor     = new Processor();
		$configuration = new ManagerConfiguration();
		$this->config  = $processor->processConfiguration($configuration, ['manager' => $config]);

		$this->adapter = $this->config['adapter'];
	}

	/**
	 * @param array $options
	 *
	 * @return Secret
	 * @throws \Psr\Cache\InvalidArgumentException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function getSecret(array $options): Secret
	{
		return $this->memoize(
			json_encode(ArrayHelper::with($options, 'key', 'path')),
			function () use ($options) {
				return $this->adapter->getSecret($options);
			}
		);
	}

	/**
	 * @param array $options
	 *
	 * @return Secret[]
	 * @throws \Psr\Cache\InvalidArgumentException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function getSecrets(array $options): array
	{
		return $this->memoize(
			json_encode(ArrayHelper::with($options, 'key', 'path')),
			function () use ($options) {
				return $this->adapter->getSecrets($options);
			}
		);
	}

	/**
	 * @param array $options
	 *
	 * @return void
	 */
	public function putSecret(array $options): void
	{
		$this->adapter->putSecret($options);
		if ($this->shouldCache()) {
			$this->getCache()->clear();
		}
	}

	/**
	 * @param array $options
	 *
	 * @return void
	 */
	public function putSecrets(array $options): void
	{
		$this->adapter->putSecrets($options);
		if ($this->shouldCache()) {
			$this->getCache()->clear();
		}
	}

	/**
	 * @return AdapterInterface[]
	 */
	public function getAdapter(): array
	{
		return $this->adapter;
	}

	/**
	 * @return array
	 */
	public function getConfig(): array
	{
		return $this->config;
	}

	/**
	 * @return bool
	 */
	protected function shouldCache(): bool
	{
		return $this->config['cache']['enabled'];
	}

	/**
	 * @return CacheItemPoolInterface|CacheInterface|null
	 */
	public function getCache()
	{
		return $this->config['cache']['instance'] ?? null;
	}

	/**
	 * @param string   $key
	 * @param callable $callback
	 * @param int|null $ttl
	 *
	 * @return mixed
	 * @throws \Psr\Cache\InvalidArgumentException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function memoize(string $key, callable $callback, int $ttl = null)
	{
		$cache = $this->getCache();
		if ($this->shouldCache()) {
			if ($cache instanceof CacheInterface && $cache->has($key)) {
				return $cache->get($key);
			}

			if ($cache instanceof CacheItemPoolInterface && $cache->hasItem($key)) {
				return $cache->getItem($key)->get();
			}
		}

		$cachedValue = $callback();
		if ($this->shouldCache()) {
			if ($cache instanceof CacheInterface) {
				$cache->set($key, $cachedValue, $ttl);
			}

			if ($cache instanceof CacheItemPoolInterface) {
				$item = $cache->getItem($key);
				$item->set($cachedValue);
				if ($ttl !== null) {
					$item->expiresAfter($ttl);
				}
			}
		}

		return $cachedValue;
	}
}
