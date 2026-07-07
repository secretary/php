<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Bundle\SecretaryBundle\EnvVar;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Manager;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;

class EnvVarProcessor implements EnvVarProcessorInterface
{
    /**
     * @var list<Manager>
     */
    private array $managers;

    private LoggerInterface $logger;

    public function __construct(
        \Traversable $managers,
        ?LoggerInterface $logger = null,
        private bool $allowMissingSecrets = false
    ) {
        $this->managers = iterator_to_array($managers);
        $this->logger   = $logger ?? new NullLogger();
    }

    /**
     * @psalm-suppress InvalidArrayOffset
     */
    public function getEnv(string $prefix, string $name, \Closure $getEnv): mixed
    {
        $parts = explode(':', $name);

        if (!array_key_exists($parts[0], $this->managers)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid manager name. Available managers: %s', $parts[0], implode(', ', array_keys($this->managers))));
        }

        $manager = $this->managers[$parts[0]];
        $key     = $getEnv($parts[1]);

        try {
            $value = $manager->getSecret($key)->getValue();
        } catch (SecretNotFoundException $e) {
            $message = sprintf('Secret "%s" was not found in manager "%s".', $key, $parts[0]);

            if (!$this->allowMissingSecrets && !str_ends_with($prefix, 'Optional')) {
                throw new EnvNotFoundException($message, 0, $e);
            }

            $this->logger->warning($message);

            return null;
        }

        if (array_key_exists(2, $parts)) {
            if (!is_array($value)) {
                throw new \InvalidArgumentException(sprintf('Index isn\'t available n %s. Value is a string.', $key));
            }

            if (!array_key_exists($parts[2], $value)) {
                throw new \InvalidArgumentException(sprintf('%s is not a valid index in that secret. Available indexes: %s', $parts[2], implode(', ', array_keys($value))));
            }
            $value = $value[$parts[2]];
        }

        return $value;
    }

    public static function getProvidedTypes(): array
    {
        return [
            'secretary'              => 'bool|int|float|string',
            'secret'                 => 'bool|int|float|string',
            'secretArray'            => 'array',
            'secretaryArray'         => 'array',
            'secretaryOptional'      => 'bool|int|float|string',
            'secretaryArrayOptional' => 'array',
        ];
    }
}
