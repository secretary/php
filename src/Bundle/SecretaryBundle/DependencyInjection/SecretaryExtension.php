<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Bundle\SecretaryBundle\DependencyInjection;

use Secretary\Adapter\Cache\PSR16Cache\PSR16CacheAdapter;
use Secretary\Adapter\Cache\PSR6Cache\PSR6CacheAdapter;
use Secretary\Bundle\SecretaryBundle\EnvVar\EnvVarProcessor;
use Secretary\Bundle\SecretaryBundle\EnvVar\EnvVarProvider;
use Secretary\Manager;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SecretaryExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $services = [];
        $default  = isset($config['adapters']['default']) ? 'default' : null;
        foreach ($config['adapters'] as $name => $arguments) {
            if ($default === null) {
                $default = $name;
            }

            $arguments['config'] = $this->replaceReferences($arguments['config']);

            if ($container->has($arguments['adapter'])) {
                $ref = new Reference($arguments['adapter']);
            } else {
                $adapter = $container->register('secretary.adapter.'.$name, $arguments['adapter']);
                $adapter->addArgument($arguments['config']);
                $adapter->setPublic(true);

                $ref = new Reference('secretary.adapter.'.$name);
            }

            if ($arguments['cache']['enabled']) {
                $adapter = $container->register(
                    'secretary.adapter.'.$name.'.cache',
                    $arguments['cache']['type'] === 'psr6' ? PSR6CacheAdapter::class : PSR16CacheAdapter::class
                );
                $adapter->addArgument($ref);
                $adapter->addArgument(new Reference($arguments['cache']['service_id']));
                $adapter->setPublic(true);

                $ref = new Reference('secretary.adapter.'.$name.'.cache');
            }

            $def = $container->register('secretary.manager.'.$name, Manager::class);
            $def->addArgument($ref);
            $def->addTag('secretary.manager');
            $def->setPublic(true);

            $services[$name] = new Reference('secretary.manager.'.$name);
        }

        if ($default !== null) {
            $alias = new Alias('secretary.manager.'.$default, true);

            $container->setAlias('secretary', $alias);
            $container->setAlias(Manager::class, $alias);
        }


        $container->register('secretary.env_var_processor', EnvVarProcessor::class)
            ->addArgument(new IteratorArgument($services))
            ->addTag('container.env_var_processor')
            ->setPublic(false);
    }

    private function replaceReferences($value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->replaceReferences($v);
            }

            return $value;
        }

        if (!is_string($value)) {
            return $value;
        }

        if (strpos($value, '@') === 0) {
            return new Reference(substr($value, 1));
        }

        return $value;
    }
}
