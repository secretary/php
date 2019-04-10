<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

namespace Secretary\Tests;

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Secretary\Adapter\AdapterInterface;
use Secretary\Adapter\Secret;
use Secretary\Manager;

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


    public function test__construct()
    {
        $manager = new Manager($this->adapter);

        $this->assertInstanceOf(Manager::class, $manager);
    }

    public function testGetSecret()
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

    public function testPutSecret()
    {
        $manager = new Manager($this->adapter);

        $this->adapter->shouldReceive('configureSharedOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('configurePutSecretOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('putSecret')->with('foo', 'bar', [])->once();

        $manager->putSecret('foo', 'bar');
        $this->assertTrue(true);
    }

    public function testDeleteSecret()
    {
        $manager = new Manager($this->adapter);

        $this->adapter->shouldReceive('configureSharedOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('configureDeleteSecretOptions')->withAnyArgs()->once();
        $this->adapter->shouldReceive('deleteSecret')->with('foo', [])->once();

        $manager->deleteSecret('foo');
        $this->assertTrue(true);
    }

    public function testGetAdapter()
    {
        $manager = new Manager($this->adapter);

        $this->assertEquals($this->adapter, $manager->getAdapter());
        $this->assertInstanceOf(AdapterInterface::class, $manager->getAdapter());
    }
}
