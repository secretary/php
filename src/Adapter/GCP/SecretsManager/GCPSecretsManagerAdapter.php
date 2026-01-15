<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter\GCP\SecretsManager;

use Google\ApiCore\ApiException;
use Google\Cloud\SecretManager\V1\Client\SecretManagerServiceClient;
use Google\Cloud\SecretManager\V1\AccessSecretVersionRequest;
use Google\Cloud\SecretManager\V1\AddSecretVersionRequest;
use Google\Cloud\SecretManager\V1\CreateSecretRequest;
use Google\Cloud\SecretManager\V1\DeleteSecretRequest;
use Google\Cloud\SecretManager\V1\Replication;
use Google\Cloud\SecretManager\V1\Replication\Automatic;
use Google\Cloud\SecretManager\V1\Secret as GCPSecret;
use Google\Cloud\SecretManager\V1\SecretPayload;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Secret;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GCPSecretsManagerAdapter.
 *
 * @package Secretary\Adapter\GCP\SecretsManager
 */
class GCPSecretsManagerAdapter extends AbstractAdapter
{
    private ?SecretManagerServiceClient $client = null;

    private array $config;

    /**
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        if (!class_exists(SecretManagerServiceClient::class)) {
            throw new \Exception('google/cloud-secret-manager is required to use the GCPSecretsManagerAdapter');
        }

        $resolver = new OptionsResolver();
        $resolver->setRequired(['project_id']);
        $resolver->setDefined(['credentials']);
        $resolver->setAllowedTypes('project_id', 'string');
        $resolver->setAllowedTypes('credentials', ['string', 'array']);

        $this->config = $resolver->resolve($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        $version = $options['version'] ?? 'latest';

        $secretVersionName = SecretManagerServiceClient::secretVersionName(
            $this->config['project_id'],
            $key,
            $version
        );

        try {
            $request = new AccessSecretVersionRequest();
            $request->setName($secretVersionName);

            $response = $this->getClient()->accessSecretVersion($request);
            $secretString = $response->getPayload()->getData();

            return new Secret(
                $key,
                static::isJson($secretString) ? json_decode($secretString, true) : $secretString
            );
        } catch (ApiException $exception) {
            if ($exception->getStatus() === 'NOT_FOUND') {
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
        $secretValue = is_array($secret->getValue())
            ? json_encode($secret->getValue()) : $secret->getValue();

        $secretName = SecretManagerServiceClient::secretName(
            $this->config['project_id'],
            $secret->getKey()
        );

        try {
            $request = new AddSecretVersionRequest();
            $request->setParent($secretName);
            $request->setPayload((new SecretPayload())->setData($secretValue));

            $this->getClient()->addSecretVersion($request);
        } catch (ApiException $e) {
            if ($e->getStatus() === 'NOT_FOUND') {
                $parentName = SecretManagerServiceClient::projectName($this->config['project_id']);

                $gcpSecret = new GCPSecret();
                $gcpSecret->setReplication(
                    (new Replication())->setAutomatic(new Automatic())
                );

                $createRequest = new CreateSecretRequest();
                $createRequest->setParent($parentName);
                $createRequest->setSecretId($secret->getKey());
                $createRequest->setSecret($gcpSecret);

                $this->getClient()->createSecret($createRequest);

                $addVersionRequest = new AddSecretVersionRequest();
                $addVersionRequest->setParent($secretName);
                $addVersionRequest->setPayload((new SecretPayload())->setData($secretValue));

                $this->getClient()->addSecretVersion($addVersionRequest);
            } else {
                throw $e;
            }
        }

        return $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSecretByKey(string $key, ?array $options = []): void
    {
        $secretName = SecretManagerServiceClient::secretName(
            $this->config['project_id'],
            $key
        );

        $request = new DeleteSecretRequest();
        $request->setName($secretName);

        $this->getClient()->deleteSecret($request);
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
        $resolver->setDefined(['version'])
            ->setAllowedTypes('version', 'string')
            ->setDefault('version', 'latest');
    }

    public function configurePutSecretOptions(OptionsResolver $resolver): void
    {
        parent::configureSharedOptions($resolver);
    }

    public function configureDeleteSecretOptions(OptionsResolver $resolver): void
    {
        parent::configureDeleteSecretOptions($resolver);
    }

    private function getClient(): SecretManagerServiceClient
    {
        if (!$this->client instanceof SecretManagerServiceClient) {
            $clientConfig = [];

            if (isset($this->config['credentials'])) {
                $clientConfig['credentials'] = $this->config['credentials'];
            }

            $this->client = new SecretManagerServiceClient($clientConfig);
        }

        return $this->client;
    }

    private static function isJson(string $str): bool
    {
        $json = json_decode($str);

        return $json && $str != $json;
    }
}
