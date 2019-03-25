<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      ${YEAR}
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary;

use Secretary\Adapter\AdapterInterface;
use Secretary\Adapter\Secret;
use Secretary\Configuration\ManagerConfiguration;
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
		$processor = new Processor();
		$configuration = new ManagerConfiguration();
		$this->config = $processor->processConfiguration($configuration, ['manager' => $config]);

		$this->adapter = $this->config['adapter']['instance'];
	}

	/**
	 * @param array $options
	 *
	 * @return Secret
	 */
	public function getSecret(array $options): Secret
	{
		return $this->adapter->getSecret($options);
	}

	/**
	 * @param array $options
	 *
	 * @return Secret[]
	 */
	public function getSecrets(array $options): array
	{
		return $this->adapter->getSecret($options);
	}

	/**
	 * @param array $options
	 *
	 * @return void
	 */
	public function putSecret(array $options): void
	{
		$this->adapter->getSecret($options);
	}

	/**
	 * @param array $options
	 *
	 * @return void
	 */
	public function putSecrets(array $options): void
	{
		$this->adapter->getSecret($options);
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
}
