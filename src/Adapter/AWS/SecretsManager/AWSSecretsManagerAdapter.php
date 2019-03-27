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
use Secretary\Adapter\SecretWithPath;
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
		if (!class_exists(SecretsManagerClient::class)) {
			throw new \Exception('aws/aws-sdk-php is required to use the AWSSecretsManagerAdapter');
		}

		$this->client = new SecretsManagerClient($config);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSecrets(array $options): array
	{
		$params = ['SecretId' => $options['path']];
		if ($options['versionId']) {
			$params['VersionId'] = $options['versionId'];
		}
		if ($options['versionStage']) {
			$params['VersionStage'] = $options['versionStage'];
		}

		$data    = $this->client->getSecretValue($params);
		$json    = json_decode($data->get('SecretString'), true);
		$secrets = [];
		foreach ($json as $key => $value) {
			$secrets[] = new SecretWithPath($key, $value, $options['path']);
		}

		return $secrets;
	}

	/**
	 * {@inheritdoc}
	 */
	public function putSecret(string $key, string $value, array $options): void
	{
		try {
			$secret   = $this->getSecrets($options);
			$newValue = [];
			foreach ($secret as $k => $v) {
				$newValue[$k] = $v;
			}
			$newValue[$key] = $value;

			$options                 = ArrayHelper::without($options, 'path');
			$options['SecretId']     = $options['path'];
			$options['SecretString'] = $newValue;

			$this->client->updateSecret($options);
		} catch (\Exception $e) {
			$options                 = ArrayHelper::without($options, 'path');
			$options['Name']         = $options['path'];
			$options['SecretString'] = json_encode([$key => $value]);

			$this->client->createSecret($options);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function putSecrets(array $options): void
	{
		$secrets = $options['secrets'];
		foreach ($secrets as $secret) {
			$opts = ['path' => $secret['path']] + ArrayHelper::without($options, 'secrets');

			$this->putSecret($secret['key'], $secret['value'], $opts);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteSecret(string $key, array $options): void
	{
		try {
			$secret   = $this->getSecrets($options);
			$newValue = [];
			foreach ($secret as $k => $v) {
				$newValue[$k] = $v;
			}
			unset($newValue[$key]);

			$options                 = ArrayHelper::without($options, 'path');
			$options['SecretId']     = $options['path'];
			$options['SecretString'] = $newValue;

			$this->client->updateSecret($options);
		} catch (\Exception $e) {
			$options                 = ArrayHelper::without($options, 'path');
			$options['SecretId']         = $options['path'];

			$this->client->deleteSecret($options);
		}
	}

    /**
     * {@inheritdoc}
     * @todo Optimize so this only runs as many times as is necessary
     */
	public function deleteSecrets(array $options): void
	{
		foreach ($options['secrets'] as $secret) {
			$this->deleteSecret($secret['key'], ['path' => $secret['path']] + $options);
		}
	}
}
