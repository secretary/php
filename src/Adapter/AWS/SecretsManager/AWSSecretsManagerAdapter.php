<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\AWS\SecretsManager;


use Aws\SecretsManager\SecretsManagerClient;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Secret;
use Secretary\Helper\ArrayHelper;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AWSSecretsManagerAdapter
 *
 * @package Secretary\Adapter\AWS\SecretsManager
 */
class AWSSecretsManagerAdapter extends AbstractAdapter
{
    /**
     * @var SecretsManagerClient
     */
    private $client;

    /**
     * @var array
     */
    private $config;

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

        $this->config = $config;
    }

    /**
     * @return SecretsManagerClient
     */
    private function getClient()
    {
        if (!$this->client instanceof SecretsManagerClient) {
            $this->client = new SecretsManagerClient($this->config);
        }

        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        $options['SecretId'] = $key;

        $data = $this->getClient()->getSecretValue($options);
        /** @var string $secretString */
        $secretString = $data->get('SecretString');

        return new Secret(
            $key,
            static::isJson($secretString) ? json_decode($data->get('SecretString'), true) : $secretString
        );
    }

    /**
     * {@inheritdoc}
     */
    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        $options['SecretString'] = is_array($secret->getValue())
            ? json_encode($secret->getValue()) : $secret->getValue();

        try {
            $options             = ArrayHelper::without($options, 'Tags');
            $options['SecretId'] = $secret->getKey();

            $this->getClient()->updateSecret($options);
        } catch (\Exception $e) {
            $options['Name'] = $secret->getKey();

            $this->getClient()->createSecret($options);
        }

        return $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecretByKey(string $key, ?array $options = []): void
    {
        $options['SecretId'] = $key;

        $this->getClient()->deleteSecret($options);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecret(Secret $secret, ?array $options = []): void
    {
        $this->deleteSecretByKey($secret->getKey(), $options);
    }

    public function configureGetSecretOptions(OptionsResolver $resolver): void
    {
        parent::configureSharedOptions($resolver);
        $resolver->setDefined(['VersionId', 'VersionStage'])
            ->setAllowedTypes('VersionId', 'string')
            ->setAllowedTypes('VersionStage', 'string');
    }

    public function configurePutSecretOptions(OptionsResolver $resolver): void
    {
        parent::configureSharedOptions($resolver);
        $resolver->setDefined(['KmsKeyId', 'Tags', 'Description'])
            ->setAllowedTypes('KmsKeyId', 'string')
            ->setAllowedTypes('Description', 'string');
    }

    public function configureDeleteSecretOptions(OptionsResolver $resolver): void
    {
        parent::configureDeleteSecretOptions($resolver);
        $resolver
            ->setDefined(['ForceDeleteWithoutRecovery', 'RecoveryWindowInDays'])
            ->setAllowedTypes('ForceDeleteWithoutRecovery', 'bool')
            ->setAllowedTypes('RecoveryWindowInDays', 'int');
    }

    private static function isJson(string $str)
    {
        $json = json_decode($str);

        return $json && $str != $json;
    }
}
