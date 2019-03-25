<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\AWS\SecretsManager;


use Aws\SecretsManager\SecretsManagerClient;
use Secretary\Adapter\AbstractPathAdapter;
use Secretary\Adapter\AWS\SecretsManager\Configuration\OptionsConfiguration;
use Secretary\Adapter\AWS\SecretsManager\Configuration\GetSecretsOptionsConfiguration;
use Secretary\Adapter\SecretWithPath;
use Secretary\Configuration\Adapter\AbstractAdapterConfiguration;
use Secretary\Configuration\Adapter\AbstractOptionsConfiguration;
use Secretary\Helper\ArrayHelper;

/**
 * Class AWSSecretsManagerAdapter
 *
 * @package Secretary\Adapter\AWS\SecretsManager
 */
class AWSSecretsManagerAdapter extends AbstractPathAdapter
{
	/**
	 * @var SecretsManagerClient
	 */
	private $client;

	/**
	 * AWSSecretsManagerAdapter constructor.
	 *
	 * @param array $config
	 *
	 * @throws \Exception
	 */
	public function __construct(array $config)
	{
		parent::__construct($config);
		if (!class_exists(SecretsManagerClient::class)) {
			throw new \Exception('aws/aws-sdk-php is required to use the AWSSecretsManagerAdapter');
		}

		$this->client = new SecretsManagerClient($config);
	}

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	protected function doGetSecrets(array $options): array
	{
		$params = ['SecretId' => $options['path']];
		if ($options['versionId']) {
			$params['VersionId'] = $options['versionId'];
		}
		if ($options['versionStage']) {
			$params['VersionStage'] = $options['versionStage'];
		}

		$data = $this->client->getSecretValue($params);
		$json = json_decode($data->get('SecretString'), true);
		$secrets = [];
		foreach ($json as $key => $value) {
			$secrets[] = new SecretWithPath($key, $value, $options['path']);
		}

		return $secrets;
	}

	/**
	 * @param array $options
	 */
	protected function doPutSecret(array $options): void
	{
		try {
			$secret = $this->getSecrets(ArrayHelper::without($options, 'key', 'value'));
			$newValue = [];
			foreach ($secret as $key => $value) {
				$newValue[$key] = $value;
			}
			$newValue[$options['key']] = $options['value'];

			$options = ArrayHelper::without($options, 'key', 'value', 'path');
			$options['SecretId'] = $options['path'];
			$options['SecretString'] = $newValue;

			$this->client->updateSecret($options);
		} catch (\Exception $e) {
			$options = ArrayHelper::without($options, 'key', 'value', 'path');
			$options['Name'] = $options['path'];
			$options['SecretString'] = json_encode([$options['key'] => $options['value']]);

			$this->client->createSecret($options);
		}
	}

	/**
	 * @param array $options
	 */
	protected function doPutSecrets(array $options): void
	{
		$secrets = $options['secrets'];
		foreach ($secrets as $secret) {
			$this->putSecret($secret + ArrayHelper::without($options, 'secrets'));
		}
	}

	/**
	 * @return AbstractOptionsConfiguration
	 */
	protected function getGetSecretsConfiguration(): AbstractOptionsConfiguration
	{
		return new GetSecretsOptionsConfiguration($this->pathRegex);
	}

	/**
	 * @return AbstractAdapterConfiguration
	 */
	protected function getConfiguration(): AbstractAdapterConfiguration
	{
		return new OptionsConfiguration();
	}
}
