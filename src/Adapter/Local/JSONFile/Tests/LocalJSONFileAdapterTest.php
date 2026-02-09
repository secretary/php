<?php

declare(strict_types=1);

namespace Secretary\Tests;

use PHPUnit\Framework\TestCase;
use Secretary\Adapter\Local\JSONFile\LocalJSONFileAdapter;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Secret;

class LocalJSONFileAdapterTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempFile = tempnam(sys_get_temp_dir(), 'secretary_test_');
        file_put_contents($this->tempFile, json_encode([
            ['key' => 'db/password', 'value' => 's3cret'],
            ['key' => 'api/token', 'value' => 'tok123', 'metadata' => ['env' => 'test']],
        ]));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }

        parent::tearDown();
    }

    public function testConstructThrowsWhenConfigIsEmpty(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Configuration is required.');

        new LocalJSONFileAdapter([]);
    }

    public function testConstructThrowsWhenFileMissing(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('`file` is a required config.');

        new LocalJSONFileAdapter(['foo' => 'bar']);
    }

    public function testGetSecretReturnsSecret(): void
    {
        $adapter = new LocalJSONFileAdapter(['file' => $this->tempFile]);

        $secret = $adapter->getSecret('db/password');

        $this->assertInstanceOf(Secret::class, $secret);
        $this->assertSame('db/password', $secret->getKey());
        $this->assertSame('s3cret', $secret->getValue());
    }

    public function testGetSecretReturnsMetadata(): void
    {
        $adapter = new LocalJSONFileAdapter(['file' => $this->tempFile]);

        $secret = $adapter->getSecret('api/token');

        $this->assertSame(['env' => 'test'], $secret->getMetadata());
    }

    public function testGetSecretThrowsWhenKeyNotFound(): void
    {
        $adapter = new LocalJSONFileAdapter(['file' => $this->tempFile]);

        $this->expectException(SecretNotFoundException::class);
        $this->expectExceptionMessage('No secret was found with the key: "nonexistent"');

        $adapter->getSecret('nonexistent');
    }

    public function testGetSecretThrowsWhenFileDoesNotExist(): void
    {
        $adapter = new LocalJSONFileAdapter(['file' => '/tmp/secretary_nonexistent_file.json']);

        $this->expectException(SecretNotFoundException::class);
        $this->expectExceptionMessage('No secret was found with the key: "any-key"');

        try {
            $adapter->getSecret('any-key');
        } catch (SecretNotFoundException $e) {
            $this->assertNotNull($e->getPrevious(), 'Expected a previous exception to be set');
            $this->assertSame('Secrets file does not exist.', $e->getPrevious()->getMessage());

            throw $e;
        }
    }

    public function testPutSecretAddsNewSecret(): void
    {
        $adapter = new LocalJSONFileAdapter(['file' => $this->tempFile]);
        $secret  = new Secret('new/key', 'new-value');

        $result = $adapter->putSecret($secret);

        $this->assertSame('new/key', $result->getKey());
        $this->assertSame('new-value', $result->getValue());
    }

    public function testPutSecretUpdatesExistingSecret(): void
    {
        $adapter = new LocalJSONFileAdapter(['file' => $this->tempFile]);
        $secret  = new Secret('db/password', 'updated');

        $adapter->putSecret($secret);

        $retrieved = $adapter->getSecret('db/password');
        $this->assertSame('updated', $retrieved->getValue());
    }

    public function testDeleteSecretByKey(): void
    {
        $adapter = new LocalJSONFileAdapter(['file' => $this->tempFile]);

        $adapter->deleteSecretByKey('db/password');

        $this->expectException(SecretNotFoundException::class);
        $adapter->getSecret('db/password');
    }

    public function testDeleteSecretByKeyThrowsWhenNotFound(): void
    {
        $adapter = new LocalJSONFileAdapter(['file' => $this->tempFile]);

        $this->expectException(SecretNotFoundException::class);
        $adapter->deleteSecretByKey('nonexistent');
    }

    public function testDeleteSecret(): void
    {
        $adapter = new LocalJSONFileAdapter(['file' => $this->tempFile]);
        $secret  = new Secret('api/token', 'tok123');

        $adapter->deleteSecret($secret);

        $this->expectException(SecretNotFoundException::class);
        $adapter->getSecret('api/token');
    }
}
