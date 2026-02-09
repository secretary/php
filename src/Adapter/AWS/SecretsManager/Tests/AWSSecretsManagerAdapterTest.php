<?php

declare(strict_types=1);

namespace Secretary\Tests;

use Aws\CommandInterface;
use Aws\Result;
use Aws\SecretsManager\Exception\SecretsManagerException;
use Aws\SecretsManager\SecretsManagerClient;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Secretary\Adapter\AWS\SecretsManager\AWSSecretsManagerAdapter;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Secret;

#[CoversClass(AWSSecretsManagerAdapter::class)]
class AWSSecretsManagerAdapterTest extends TestCase
{
    private AWSSecretsManagerAdapter $adapter;

    private SecretsManagerClient|MockInterface $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = \Mockery::mock(SecretsManagerClient::class);
        $this->adapter = new AWSSecretsManagerAdapter([]);

        $reflection = new \ReflectionProperty(AWSSecretsManagerAdapter::class, 'client');
        $reflection->setValue($this->adapter, $this->client);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testGetSecretWithStringValue(): void
    {
        $result = new Result(['SecretString' => 'my-secret-value']);

        $this->client
            ->shouldReceive('getSecretValue')
            ->with(\Mockery::on(fn (array $opts) => $opts['SecretId'] === 'my/key'))
            ->once()
            ->andReturn($result);

        $secret = $this->adapter->getSecret('my/key');

        $this->assertInstanceOf(Secret::class, $secret);
        $this->assertEquals('my/key', $secret->getKey());
        $this->assertEquals('my-secret-value', $secret->getValue());
    }

    public function testGetSecretWithJsonValue(): void
    {
        $jsonData = ['username' => 'admin', 'password' => 'secret123'];
        $result = new Result(['SecretString' => json_encode($jsonData)]);

        $this->client
            ->shouldReceive('getSecretValue')
            ->with(\Mockery::on(fn (array $opts) => $opts['SecretId'] === 'db/credentials'))
            ->once()
            ->andReturn($result);

        $secret = $this->adapter->getSecret('db/credentials');

        $this->assertInstanceOf(Secret::class, $secret);
        $this->assertEquals('db/credentials', $secret->getKey());
        $this->assertEquals($jsonData, $secret->getValue());
    }

    public function testGetSecretThrowsSecretNotFoundException(): void
    {
        $this->expectException(SecretNotFoundException::class);

        $command = \Mockery::mock(CommandInterface::class);
        $exception = new SecretsManagerException(
            'Error',
            $command,
            ['message' => "Secrets Manager can\u{2019}t find the specified secret"]
        );

        $this->client
            ->shouldReceive('getSecretValue')
            ->once()
            ->andThrow($exception);

        $this->adapter->getSecret('nonexistent/key');
    }

    public function testGetSecretRethrowsOtherExceptions(): void
    {
        $this->expectException(SecretsManagerException::class);

        $command = \Mockery::mock(CommandInterface::class);
        $exception = new SecretsManagerException(
            'Access denied',
            $command,
            ['message' => 'User is not authorized']
        );

        $this->client
            ->shouldReceive('getSecretValue')
            ->once()
            ->andThrow($exception);

        $this->adapter->getSecret('forbidden/key');
    }

    public function testPutSecretUpdatesExisting(): void
    {
        $secret = new Secret('my/key', 'my-value');

        $this->client
            ->shouldReceive('updateSecret')
            ->with(\Mockery::on(function (array $opts) {
                return $opts['SecretId'] === 'my/key'
                    && $opts['SecretString'] === 'my-value';
            }))
            ->once();

        $result = $this->adapter->putSecret($secret);

        $this->assertSame($secret, $result);
    }

    public function testPutSecretCreatesWhenUpdateFails(): void
    {
        $secret = new Secret('new/key', 'new-value');

        $this->client
            ->shouldReceive('updateSecret')
            ->once()
            ->andThrow(new \Exception('Secret not found'));

        $this->client
            ->shouldReceive('createSecret')
            ->with(\Mockery::on(function (array $opts) {
                return $opts['Name'] === 'new/key'
                    && $opts['SecretString'] === 'new-value';
            }))
            ->once();

        $result = $this->adapter->putSecret($secret);

        $this->assertSame($secret, $result);
    }

    public function testPutSecretWithArrayValue(): void
    {
        $value = ['user' => 'admin', 'pass' => 'secret'];
        $secret = new Secret('my/key', $value);

        $this->client
            ->shouldReceive('updateSecret')
            ->with(\Mockery::on(function (array $opts) use ($value) {
                return $opts['SecretString'] === json_encode($value);
            }))
            ->once();

        $result = $this->adapter->putSecret($secret);

        $this->assertSame($secret, $result);
    }

    public function testDeleteSecretByKey(): void
    {
        $this->client
            ->shouldReceive('deleteSecret')
            ->with(\Mockery::on(fn (array $opts) => $opts['SecretId'] === 'my/key'))
            ->once();

        $this->adapter->deleteSecretByKey('my/key');

        $this->assertTrue(true);
    }

    public function testDeleteSecret(): void
    {
        $secret = new Secret('my/key', 'value');

        $this->client
            ->shouldReceive('deleteSecret')
            ->with(\Mockery::on(fn (array $opts) => $opts['SecretId'] === 'my/key'))
            ->once();

        $this->adapter->deleteSecret($secret);

        $this->assertTrue(true);
    }
}
