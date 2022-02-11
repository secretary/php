<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter\Hashicorp\Vault\Client;

use GuzzleHttp\HandlerStack;
use Secretary\Adapter\Hashicorp\Vault\Client\Middleware\AppRoleAuthenticator;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Client extends \GuzzleHttp\Client
{
    /**
     * Client constructor.
     */
    public function __construct(array $config)
    {
        $config = $this->validateOptions($config);

        $baseUri = rtrim($config['address'], '/');

        $stack   = HandlerStack::create();
        $options = ['base_uri' => $baseUri, 'stack' => $stack];

        if (!empty($config['credentials']['token'])) {
            $options['headers'] = ['X-Vault-Token' => $config['credentials']['token']];
        }

        if (!empty($config['credentials']['appRole'])) {
            ['roleId' => $roleId, 'secretId' => $secretId] = $config['credentials']['appRole'];

            if (!empty($roleId) && !empty($secretId)) {
                $stack->push(new AppRoleAuthenticator($this, $roleId, $secretId));
            }
        }

        parent::__construct($options);
    }

    /**
     * @todo Add options for SSL cert
     */
    private function validateOptions(array $config): array
    {
        $addr  = getenv('VAULT_ADDR');
        $token = getenv('VAULT_TOKEN');

        $resolver = new OptionsResolver();
        $resolver
            ->setDefined('address')
            ->setAllowedTypes('address', 'string');

        if ($addr !== false) {
            $resolver->setDefault('address', $addr);
        }

        $resolver->setDefault(
            'credentials',
            function (OptionsResolver $credentials) use ($token) {
                $credentials
                    ->setRequired('token')
                    ->setAllowedTypes('token', 'string');

                if ($token !== false) {
                    $credentials->setDefault('token', $token);
                }

                $credentials->setDefault(
                    'appRole',
                    function (OptionsResolver $appRole) {
                        $appRole
                            ->setDefault('roleId', '')
                            ->setRequired('roleId')
                            ->setAllowedTypes('roleId', 'string');

                        $appRole
                            ->setDefault('secretId', '')
                            ->setRequired('secretId')
                            ->setAllowedTypes('secretId', 'string');
                    }
                );
            }
        );

        return $resolver->resolve($config);
    }
}
