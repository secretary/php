<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Tests;

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Secretary\Adapter\AdapterInterface;
use Secretary\Exception\SecretNotFoundException;
use Secretary\Manager;
use Secretary\Secret;

/**
 * @internal
 * @coversNothing
 */
class ManagerTest extends TestCase
{
    /**
     * @var AdapterInterface|MockInterface
     */
    private $adapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = \Mockery::mock(AdapterInterface::class);
    }

    /**
     * @psalm-suppress RedundantCondition
     */
    public function testConstruct(): void
    {
        $manager = new Manager($this->adapter);

        $this->assertInstanceOf(Manager::class, $manager);
    }

    /**
     * @psalm-suppress RedundantCondition
     */
    public function testGetSecret(): void
    {
        $secret  = new Secret('foo', 'bar');
        $manager = new Manager($this->adapter);

        $this->adapter->shouldReceive('configureSharedOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('configureGetSecretOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('getSecret')->with('foo', [])->andReturn($secret)->once();

        $result = $manager->getSecret('foo');
        $this->assertInstanceOf(Secret::class, $result);
        $this->assertEquals($secret, $result);
    }

    public function testGetBadSecret(): void
    {
        $this->expectException(SecretNotFoundException::class);
        $this->expectExceptionMessage('No secret was found with the key: "foo"');

        $manager = new Manager($this->adapter);

        $this->adapter->shouldReceive('configureSharedOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('configureGetSecretOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('getSecret')
            ->with('foo', [])->andThrow(new SecretNotFoundException('foo'))->once();

        $manager->getSecret('foo');
    }

    public function testPutSecret(): void
    {
        $manager = new Manager($this->adapter);
        $secret  = new Secret('foo', 'bar');

        $this->adapter->shouldReceive('configureSharedOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('configurePutSecretOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('putSecret')->with($secret, [])->andReturn($secret)->once();

        $response = $manager->putSecret($secret);
        $this->assertEquals($secret, $response);
    }

    public function testDeleteSecretByKey(): void
    {
        $manager = new Manager($this->adapter);
        $secret  = new Secret('foo', '');

        $this->adapter->shouldReceive('configureSharedOptions')->withAnyArgs()->twice();

        $this->adapter->shouldReceive('configureGetSecretOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('getSecret')->with('foo', [])->andReturn($secret)->once();

        $this->adapter->shouldReceive('configureDeleteSecretOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('deleteSecret')->with($secret, [])->once();

        $manager->deleteSecretByKey('foo');
        $this->assertTrue(true);
    }

    public function testDeleteSecret(): void
    {
        $manager = new Manager($this->adapter);
        $secret  = new Secret('foo', 'bar');

        $this->adapter->shouldReceive('configureSharedOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('configureDeleteSecretOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('deleteSecret')->with($secret, [])->once();

        $manager->deleteSecret($secret);
        $this->assertTrue(true);
    }

    public function testGetAdapter(): void
    {
        $manager = new Manager($this->adapter);

        $this->assertEquals($this->adapter, $manager->getAdapter());
        $this->assertInstanceOf(AdapterInterface::class, $manager->getAdapter());
    }
}
