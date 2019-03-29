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
use Secretary\Adapter\Secret;
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
    public function getSecret(string $key, ?array $options = []): Secret
    {
        $options['SecretId'] = $key;

        $data = $this->client->getSecretValue($options);
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
    public function putSecret(string $key, $value, ?array $options = []): void
    {
        $options['SecretString'] = is_array($value) ? json_encode($value) : $value;
        try {
            $options['SecretId'] = $key;

            $this->client->updateSecret($options);
        } catch (\Exception $e) {
            $options = ArrayHelper::without($options, 'Tags');
            $options['Name'] = $key;

            $this->client->createSecret($options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecret(string $key, ?array $options = []): void
    {
        $options['SecretId'] = $key;

        $this->client->deleteSecret($options);
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
            ->setAllowedTypes('Description', 'string')
            ->setDefault(
                'Tags',
                function (OptionsResolver $options) {
                    $options->setRequired(['Key', 'Value']);
                }
            );
    }

    public function configureDeleteSecretOptions(OptionsResolver $resolver): void
    {
        parent::configureSharedOptions($resolver);
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
