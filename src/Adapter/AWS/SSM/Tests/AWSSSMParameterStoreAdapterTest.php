<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter\AWS\SSM\Tests;

use Aws\Result;
use Aws\Ssm\Exception\SsmException;
use Aws\Ssm\SsmClient;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Secretary\Adapter\AWS\SSM\AWSSSMParameterStoreAdapter;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Secret;

final class AWSSSMParameterStoreAdapterTest extends MockeryTestCase
{
    private function adapterWithClient(SsmClient $client): AWSSSMParameterStoreAdapter
    {
        $adapter = new AWSSSMParameterStoreAdapter([]);

        $ref  = new \ReflectionObject($adapter);
        $prop = $ref->getProperty('client');
        $prop->setAccessible(true);
        $prop->setValue($adapter, $client);

        return $adapter;
    }

    private function notFound(): SsmException
    {
        $exception = Mockery::mock(SsmException::class);
        $exception->shouldReceive('getAwsErrorCode')->andReturn('ParameterNotFound');

        return $exception;
    }

    public function testGetSecretReturnsPlainString(): void
    {
        $client = Mockery::mock(SsmClient::class);
        $client->shouldReceive('getParameter')
            ->once()
            ->with(['Name' => 'foo', 'WithDecryption' => true])
            ->andReturn(new Result(['Parameter' => ['Value' => 'bar']]));

        $secret = $this->adapterWithClient($client)->getSecret('foo');

        $this->assertSame('foo', $secret->getKey());
        $this->assertSame('bar', $secret->getValue());
    }

    public function testGetSecretDecodesJson(): void
    {
        $client = Mockery::mock(SsmClient::class);
        $client->shouldReceive('getParameter')
            ->once()
            ->with(['Name' => 'baz', 'WithDecryption' => true])
            ->andReturn(new Result(['Parameter' => ['Value' => json_encode(['foobar' => 'baz'])]]));

        $secret = $this->adapterWithClient($client)->getSecret('baz');

        $this->assertSame(['foobar' => 'baz'], $secret->getValue());
    }

    public function testGetSecretThrowsWhenMissing(): void
    {
        $client = Mockery::mock(SsmClient::class);
        $client->shouldReceive('getParameter')
            ->once()
            ->andThrow($this->notFound());

        $this->expectException(SecretNotFoundException::class);
        $this->adapterWithClient($client)->getSecret('bar');
    }

    public function testPutSecretWritesSecureStringByDefault(): void
    {
        $client = Mockery::mock(SsmClient::class);
        $client->shouldReceive('putParameter')
            ->once()
            ->with([
                'Type'      => 'SecureString',
                'Overwrite' => true,
                'Value'     => 'foobar',
                'Name'      => 'test',
            ])
            ->andReturn(new Result([]));

        $secret = new Secret('test', 'foobar');
        $result = $this->adapterWithClient($client)->putSecret($secret);

        $this->assertSame($secret->getKey(), $result->getKey());
    }

    public function testPutSecretEncodesArrayValue(): void
    {
        $client = Mockery::mock(SsmClient::class);
        $client->shouldReceive('putParameter')
            ->once()
            ->with([
                'Type'      => 'SecureString',
                'Overwrite' => true,
                'Value'     => json_encode(['a' => 'b']),
                'Name'      => 'obj',
            ])
            ->andReturn(new Result([]));

        $result = $this->adapterWithClient($client)->putSecret(new Secret('obj', ['a' => 'b']));

        $this->assertSame('obj', $result->getKey());
    }

    public function testDeleteSecretCallsDeleteParameter(): void
    {
        $client = Mockery::mock(SsmClient::class);
        $client->shouldReceive('deleteParameter')
            ->once()
            ->with(['Name' => 'test'])
            ->andReturn(new Result([]));

        $this->adapterWithClient($client)->deleteSecret(new Secret('test', 'foobar'));
        $this->assertTrue(true);
    }
}
