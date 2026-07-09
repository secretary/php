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
use Psr\Log\AbstractLogger;
use Secretary\Adapter\AbstractAdapter;
use Secretary\Bundle\SecretaryBundle\EnvVar\EnvVarProcessor;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Manager;
use Secretary\Secret;
use Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;

#[CoversClass(EnvVarProcessor::class)]
class EnvVarProcessorTest extends TestCase
{
    private SpyLogger $logger;

    protected function setUp(): void
    {
        $this->logger = new SpyLogger();
    }

    public function testMissingSecretThrowsEnvNotFoundException(): void
    {
        $processor = $this->createProcessor();

        try {
            $processor->getEnv('secretary', 'default:MISSING_KEY', fn (string $name) => $name);
            $this->fail('Expected EnvNotFoundException to be thrown');
        } catch (EnvNotFoundException $e) {
            $this->assertStringContainsString('MISSING_KEY', $e->getMessage());
            $this->assertStringContainsString('default', $e->getMessage());
            $this->assertInstanceOf(SecretNotFoundException::class, $e->getPrevious());
        }
    }

    public function testOptionalPrefixLogsWarningAndReturnsNull(): void
    {
        $processor = $this->createProcessor();

        $value = $processor->getEnv('secretaryOptional', 'default:MISSING_KEY', fn (string $name) => $name);

        $this->assertNull($value);
        $this->assertCount(1, $this->logger->records);
        $this->assertSame('warning', $this->logger->records[0]['level']);
        $this->assertStringContainsString('MISSING_KEY', $this->logger->records[0]['message']);
    }

    public function testArrayOptionalPrefixLogsWarningAndReturnsNull(): void
    {
        $processor = $this->createProcessor();

        $value = $processor->getEnv('secretaryArrayOptional', 'default:MISSING_KEY', fn (string $name) => $name);

        $this->assertNull($value);
        $this->assertCount(1, $this->logger->records);
        $this->assertSame('warning', $this->logger->records[0]['level']);
    }

    public function testAllowMissingSecretsMakesStandardPrefixGraceful(): void
    {
        $processor = $this->createProcessor(allowMissingSecrets: true);

        $value = $processor->getEnv('secretary', 'default:MISSING_KEY', fn (string $name) => $name);

        $this->assertNull($value);
        $this->assertCount(1, $this->logger->records);
        $this->assertSame('warning', $this->logger->records[0]['level']);
    }

    public function testMissingSecretWithoutLoggerStillResolvesNull(): void
    {
        $manager   = new Manager(new InMemoryAdapter([]));
        $processor = new EnvVarProcessor(new \ArrayIterator(['default' => $manager]));

        $value = $processor->getEnv('secretaryOptional', 'default:MISSING_KEY', fn (string $name) => $name);

        $this->assertNull($value);
    }

    public function testExistingSecretResolvesNormally(): void
    {
        $processor = $this->createProcessor(['DB_PASS' => 'hunter2']);

        $value = $processor->getEnv('secretary', 'default:DB_PASS', fn (string $name) => $name);

        $this->assertSame('hunter2', $value);
        $this->assertCount(0, $this->logger->records);
    }

    public function testExistingSecretResolvesNormallyWithOptionalPrefix(): void
    {
        $processor = $this->createProcessor(['DB_PASS' => 'hunter2']);

        $value = $processor->getEnv('secretaryOptional', 'default:DB_PASS', fn (string $name) => $name);

        $this->assertSame('hunter2', $value);
        $this->assertCount(0, $this->logger->records);
    }

    public function testOtherExceptionsStillBubbleUp(): void
    {
        $manager   = new Manager(new ThrowingAdapter());
        $processor = new EnvVarProcessor(new \ArrayIterator(['default' => $manager]), $this->logger);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('adapter blew up');

        $processor->getEnv('secretary', 'default:ANY_KEY', fn (string $name) => $name);
    }

    public function testOtherExceptionsStillBubbleUpWithOptionalPrefix(): void
    {
        $manager   = new Manager(new ThrowingAdapter());
        $processor = new EnvVarProcessor(new \ArrayIterator(['default' => $manager]), $this->logger);

        $this->expectException(\RuntimeException::class);

        $processor->getEnv('secretaryOptional', 'default:ANY_KEY', fn (string $name) => $name);
    }

    public function testGetProvidedTypesIncludesOptionalPrefixes(): void
    {
        $types = EnvVarProcessor::getProvidedTypes();

        $this->assertSame('bool|int|float|string', $types['secretaryOptional']);
        $this->assertSame('array', $types['secretaryArrayOptional']);
    }

    /**
     * @param array<string, array|string> $secrets
     */
    private function createProcessor(array $secrets = [], bool $allowMissingSecrets = false): EnvVarProcessor
    {
        $manager = new Manager(new InMemoryAdapter($secrets));

        return new EnvVarProcessor(new \ArrayIterator(['default' => $manager]), $this->logger, $allowMissingSecrets);
    }
}

class SpyLogger extends AbstractLogger
{
    /** @var list<array{level: string, message: string, context: array}> */
    public array $records = [];

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => (string) $level, 'message' => (string) $message, 'context' => $context];
    }
}

class InMemoryAdapter extends AbstractAdapter
{
    /**
     * @param array<string, array|string> $secrets
     */
    public function __construct(private array $secrets)
    {
    }

    public function getSecret(string $key, ?array $options = []): Secret
    {
        if (!array_key_exists($key, $this->secrets)) {
            throw new SecretNotFoundException($key);
        }

        return new Secret($key, $this->secrets[$key]);
    }

    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        return $secret;
    }

    public function deleteSecret(Secret $secret, ?array $options = []): void
    {
    }
}

class ThrowingAdapter extends AbstractAdapter
{
    public function getSecret(string $key, ?array $options = []): Secret
    {
        throw new \RuntimeException('adapter blew up');
    }

    public function putSecret(Secret $secret, ?array $options = []): Secret
    {
        return $secret;
    }

    public function deleteSecret(Secret $secret, ?array $options = []): void
    {
    }
}
