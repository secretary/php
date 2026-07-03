<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter\AWS\SSM;

use Aws\Ssm\Exception\SsmException;
use Aws\Ssm\SsmClient;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Secret;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AWSSSMParameterStoreAdapter.
 *
 * @package Secretary\Adapter\AWS\SSM
 */
class AWSSSMParameterStoreAdapter extends AbstractAdapter
{
    private ?SsmClient $client = null;

    private array $config;

    /**
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        if (!class_exists(SsmClient::class)) {
            throw new \Exception('aws/aws-sdk-php is required to use the AWSSSMParameterStoreAdapter');
        }

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        $options['Name']           = $key;
        $options['WithDecryption'] = $options['WithDecryption'] ?? true;

        try {
            $data = $this->getClient()->getParameter($options);

            /** @var string $value */
            $value = $data->search('Parameter.Value');

            return new Secret(
                $key,
                static::isJson($value) ? json_decode($value, true) : $value
            );
        } catch (SsmException $exception) {
            if ($exception->getAwsErrorCode() === 'ParameterNotFound') {
                throw new SecretNotFoundException($key, $exception);
            }

            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        $options['Type']      = $options['Type']      ?? 'SecureString';
        $options['Overwrite'] = $options['Overwrite'] ?? true;
        $options['Value']     = is_array($secret->getValue())
            ? json_encode($secret->getValue()) : $secret->getValue();
        $options['Name'] = $secret->getKey();

        $this->getClient()->putParameter($options);

        return $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecretByKey(string $key, ?array $options = []): void
    {
        $options['Name'] = $key;

        $this->getClient()->deleteParameter($options);
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
        $resolver->setDefined(['WithDecryption'])
            ->setAllowedTypes('WithDecryption', 'bool');
    }

    public function configurePutSecretOptions(OptionsResolver $resolver): void
    {
        parent::configureSharedOptions($resolver);
        $resolver->setDefined(['Type', 'KeyId', 'Tags', 'Description', 'Tier'])
            ->setAllowedTypes('Type', 'string')
            ->setAllowedTypes('KeyId', 'string')
            ->setAllowedTypes('Description', 'string')
            ->setAllowedTypes('Tier', 'string');
    }

    public function configureDeleteSecretOptions(OptionsResolver $resolver): void
    {
        parent::configureDeleteSecretOptions($resolver);
    }

    private function getClient(): SsmClient
    {
        if (!$this->client instanceof SsmClient) {
            $this->client = new SsmClient($this->config);
        }

        return $this->client;
    }

    private static function isJson(string $str): bool
    {
        $json = json_decode($str);

        return $json && $str != $json;
    }
}
