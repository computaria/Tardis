<?php

namespace Pascutti\Tardis\Proxy;

use Doctrine\Common\Cache;
use Pascutti\Tardis\Identity;
use Pascutti\Tardis\Tests\Fixture;

class CacheFetchTest extends \PHPUnit_Framework_TestCase
{
    const EXISTING_VALUE = 'It’s not the time that matters, it’s the person.';
    const EXISTING_KEY = 'S03E06';

    /**
     * @var Pascutti\Tardis\Tests\Fixture\Doctor
     */
    private $proxy = null;
    /**
     * @var Pascutti\Tardis\Tests\Fixture\Doctor
     */
    private $real = null;
    /**
     * @var string
     */
    private $methodName = 'salute';
    /**
     * @var array
     */
    private $methodArguments = ['name' => 'Amy Pond'];
    /**
     * @var boolean
     */
    private $returnEarly = false;

    public function setUp()
    {
        $this->real = new Fixture\Doctor(
            'Amy Pond',
            'Geronimo'
        );

        $this->proxy = clone $this->real;

        $allMethods = [];
        $this->cacheAdapter = $this->getMock(
            'Doctrine\Common\Cache\Cache',
            $allMethods
        );

        $this->identityGenerator = new Identity\AlwaysTheSameValue(self::EXISTING_KEY);
    }

    /**
     * @test
     */
    public function non_cached_value_does_not_get_returned()
    {
        $this->cacheAdapter->expects($this->once())
            ->method('contains')
            ->with(self::EXISTING_KEY)
            ->willReturn(false);
        $this->cacheAdapter->expects($this->never())
            ->method('fetch');

        $sufixInterceptor = new CacheFetch($this->cacheAdapter, $this->identityGenerator);
        $returnedValue = $sufixInterceptor($this->proxy, $this->real, $this->methodName, $this->methodArguments, $this->returnEarly);

        $this->assertEmpty(
            $returnedValue,
            'Value not on cache should not get returned.'
        );
        $this->assertFalse(
            $this->returnEarly,
            'A non cached value should not get returned early, procceding the call on the real object.'
        );
    }

    /**
     * @test
     */
    public function cached_value_gets_returned_early()
    {
        $this->cacheAdapter->expects($this->once())
            ->method('contains')
            ->with(self::EXISTING_KEY)
            ->willReturn(true);
        $this->cacheAdapter->expects($this->once())
            ->method('fetch')
            ->with(self::EXISTING_KEY)
            ->willReturn(self::EXISTING_VALUE);

        $sufixInterceptor = new CacheFetch($this->cacheAdapter, $this->identityGenerator);
        $returnedValue = $sufixInterceptor($this->proxy, $this->real, $this->methodName, $this->methodArguments, $this->returnEarly);

        $this->assertEquals(
            self::EXISTING_VALUE,
            $returnedValue,
            'Value returned should be the same one cached.'
        );
        $this->assertTrue(
            $this->returnEarly,
            'Value on cache should be returned early.'
        );
    }

}
