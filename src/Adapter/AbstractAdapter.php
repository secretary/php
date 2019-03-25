<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Secretary\Configuration\Adapter\AbstractAdapterConfiguration;
use Secretary\Configuration\Adapter\AbstractOptionsConfiguration;
use Secretary\Configuration\Adapter\GetSecretOptionsConfiguration;
use Secretary\Configuration\Adapter\GetSecretsOptionsConfiguration;
use Secretary\Configuration\Adapter\PutSecretOptionsConfiguration;
use Secretary\Configuration\Adapter\PutSecretsOptionsConfiguration;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class AbstractAdapter
 *
 * @package Secretary\Adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
	/**
	 * @var array
	 */
	protected $config;

	/**
	 * AbstractAdapter constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$processor     = new Processor();
		$configuration = $this->getConfiguration();
		$this->config  = $processor->processConfiguration($configuration, ['adapter' => $config]);
	}

	/**
	 * @param array $options
	 *
	 * @return Secret
	 */
	public final function getSecret(array $options): Secret
	{
		return $this->doGetSecret($this->processOptions($options, $this->getGetSecretConfiguration()));
	}

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	public final function getSecrets(array $options): array
	{
		return $this->doGetSecrets($this->processOptions($options, $this->getGetSecretsConfiguration()));
	}

	/**
	 * @param array $options
	 */
	public final function putSecret(array $options): void
	{
		$this->doPutSecret($this->processOptions($options, $this->getPutSecretConfiguration()));
	}

	/**
	 * @param array $options
	 */
	public final function putSecrets(array $options): void
	{
		$this->doPutSecrets($this->processOptions($options, $this->getPutSecretsConfiguration()));
	}

	/**
	 * @param array $options
	 *
	 * @return Secret
	 */
	protected abstract function doGetSecret(array $options): Secret;

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	protected abstract function doGetSecrets(array $options): array;

	/**
	 * @param array $options
	 */
	protected abstract function doPutSecret(array $options): void;

	/**
	 * @param array $options
	 */
	protected abstract function doPutSecrets(array $options): void;

	/**
	 * @return AbstractAdapterConfiguration
	 */
	protected abstract function getConfiguration(): AbstractAdapterConfiguration;

	/**
	 * @return AbstractOptionsConfiguration
	 */
	protected function getGetSecretConfiguration(): AbstractOptionsConfiguration
	{
		return new GetSecretOptionsConfiguration();
	}

	/**
	 * @return AbstractOptionsConfiguration
	 */
	protected function getGetSecretsConfiguration(): AbstractOptionsConfiguration
	{
		return new GetSecretsOptionsConfiguration();
	}

	/**
	 * @return AbstractOptionsConfiguration
	 */
	protected function getPutSecretConfiguration(): AbstractOptionsConfiguration
	{
		return new PutSecretOptionsConfiguration();
	}

	/**
	 * @return AbstractOptionsConfiguration
	 */
	protected function getPutSecretsConfiguration(): AbstractOptionsConfiguration
	{
		return new PutSecretsOptionsConfiguration();
	}

	/**
	 * @param array                  $options
	 * @param ConfigurationInterface $configuration
	 * @param string                 $rootKey
	 *
	 * @return array
	 */
	private function processOptions(
		array $options,
		ConfigurationInterface $configuration,
		string $rootKey = 'options'
	): array {
		$processor = new Processor();

		return $processor->processConfiguration($configuration, [$rootKey => $options]);
	}
}
