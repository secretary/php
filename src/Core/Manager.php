<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary;

use Secretary\Adapter\AdapterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Manager.
 *
 * @package Secretary
 */
class Manager
{
    private AdapterInterface $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @throws Exception\SecretNotFoundException
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        $resolver = new OptionsResolver();
        $this->adapter->configureSharedOptions($resolver);
        $this->adapter->configureGetSecretOptions($resolver);

        return $this->adapter->getSecret($key, $resolver->resolve($options));
    }

    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        $resolver = new OptionsResolver();
        $this->adapter->configureSharedOptions($resolver);
        $this->adapter->configurePutSecretOptions($resolver);

        return $this->adapter->putSecret($secret, $resolver->resolve($options));
    }

    /**
     * @throws Exception\SecretNotFoundException
     */
    public function deleteSecretByKey(string $key, ?array $options = []): void
    {
        $secret = $this->getSecret($key, $options);

        $resolver = new OptionsResolver();
        $this->adapter->configureSharedOptions($resolver);
        $this->adapter->configureDeleteSecretOptions($resolver);

        $this->adapter->deleteSecret($secret, $resolver->resolve($options));
    }

    public function deleteSecret(Secret $secret, ?array $options = []): void
    {
        $resolver = new OptionsResolver();
        $this->adapter->configureSharedOptions($resolver);
        $this->adapter->configureDeleteSecretOptions($resolver);

        $this->adapter->deleteSecret($secret, $resolver->resolve($options));
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }
}
