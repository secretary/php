<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Secretary\Bundle\SecretaryBundle\DependencyInjection\SecretaryExtension;
use Secretary\Bundle\SecretaryBundle\EnvVar\EnvVarProcessor;
use Secretary\Bundle\SecretaryBundle\SecretaryBundle;
use Secretary\Manager;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(SecretaryBundle::class)]
#[CoversClass(SecretaryExtension::class)]
class SecretaryBundleTest extends TestCase
{
    public function testBundleRegistersExtension(): void
    {
        $bundle = new SecretaryBundle();

        $this->assertInstanceOf(SecretaryBundle::class, $bundle);
    }

    public function testExtensionRegistersServices(): void
    {
        $container = new ContainerBuilder();
        $extension = new SecretaryExtension();

        $extension->load([
            [
                'adapters' => [
                    'default' => [
                        'adapter' => 'Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter',
                        'config'  => [
                            'region'  => 'us-east-1',
                            'version' => '2017-10-17',
                        ],
                        'cache' => [
                            'enabled' => false,
                        ],
                    ],
                ],
            ],
        ], $container);

        $this->assertTrue($container->has('secretary.adapter.default'));
        $this->assertTrue($container->has('secretary.manager.default'));
        $this->assertTrue($container->has('secretary'));
        $this->assertTrue($container->has(Manager::class));
        $this->assertTrue($container->has('secretary.env_var_processor'));
    }

    public function testExtensionRegistersMultipleAdapters(): void
    {
        $container = new ContainerBuilder();
        $extension = new SecretaryExtension();

        $extension->load([
            [
                'adapters' => [
                    'aws' => [
                        'adapter' => 'Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter',
                        'config'  => [
                            'region'  => 'us-east-1',
                            'version' => '2017-10-17',
                        ],
                        'cache' => [
                            'enabled' => false,
                        ],
                    ],
                    'default' => [
                        'adapter' => 'Secretary\Adapter\Chain\ChainAdapter',
                        'config'  => [],
                        'cache'   => [
                            'enabled' => false,
                        ],
                    ],
                ],
            ],
        ], $container);

        $this->assertTrue($container->has('secretary.adapter.aws'));
        $this->assertTrue($container->has('secretary.manager.aws'));
        $this->assertTrue($container->has('secretary.adapter.default'));
        $this->assertTrue($container->has('secretary.manager.default'));
        // 'default' adapter should be aliased to 'secretary'
        $this->assertTrue($container->has('secretary'));
    }

    public function testFirstAdapterBecomesDefaultWhenNoDefaultDefined(): void
    {
        $container = new ContainerBuilder();
        $extension = new SecretaryExtension();

        $extension->load([
            [
                'adapters' => [
                    'aws' => [
                        'adapter' => 'Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter',
                        'config'  => [],
                        'cache'   => [
                            'enabled' => false,
                        ],
                    ],
                ],
            ],
        ], $container);

        // 'aws' should be aliased as the default since no 'default' adapter exists
        $alias = $container->getAlias('secretary');
        $this->assertEquals('secretary.manager.aws', (string) $alias);
    }

    public function testEnvVarProcessorIsRegistered(): void
    {
        $container = new ContainerBuilder();
        $extension = new SecretaryExtension();

        $extension->load([
            [
                'adapters' => [
                    'default' => [
                        'adapter' => 'Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter',
                        'config'  => [],
                        'cache'   => [
                            'enabled' => false,
                        ],
                    ],
                ],
            ],
        ], $container);

        $definition = $container->getDefinition('secretary.env_var_processor');
        $this->assertEquals(EnvVarProcessor::class, $definition->getClass());
    }
}
