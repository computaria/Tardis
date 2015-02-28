<?php

namespace Pascutti\Tardis\Proxy;

use Doctrine\Common\Cache;
use Pascutti\Tardis\Identity;
use Pascutti\Tardis\Tests\Fixture;

class CacheSaveTest extends \PHPUnit_Framework_TestCase
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
    public function value_gets_cached()
    {
        $this->cacheAdapter->expects($this->once())
            ->method('contains')
            ->with(self::EXISTING_KEY)
            ->willReturn(false);
        $this->cacheAdapter->expects($this->once())
            ->method('save')
            ->with(self::EXISTING_KEY, self::EXISTING_VALUE);

        $sufixInterceptor = new CacheSave($this->cacheAdapter, $this->identityGenerator);
        $returnValue = self::EXISTING_VALUE;
        $returnedValue = $sufixInterceptor($this->proxy, $this->real, $this->methodName, $this->methodArguments, $returnValue, $this->returnEarly);

        $this->assertEquals(
            $returnValue,
            $returnedValue,
            'Value returned should be the same one returned form the real object.'
        );
        $this->assertFalse(
            $this->returnEarly,
            'Sufix interceptor should never return an early value.'
        );
    }

    /**
     * @test
     */
    public function cached_value_does_not_get_cached_again()
    {
        $this->cacheAdapter->expects($this->once())
            ->method('contains')
            ->with(self::EXISTING_KEY)
            ->willReturn(true);
        $this->cacheAdapter->expects($this->never())
            ->method('save');

        $sufixInterceptor = new CacheSave($this->cacheAdapter, $this->identityGenerator);
        $returnValue = self::EXISTING_VALUE;
        $returnedValue = $sufixInterceptor($this->proxy, $this->real, $this->methodName, $this->methodArguments, $returnValue, $this->returnEarly);

        $this->assertEquals(
            $returnValue,
            $returnedValue,
            'Value returned should be the same one returned form the real object.'
        );
        $this->assertFalse(
            $this->returnEarly,
            'Sufix interceptor should never return an early value.'
        );
    }

}
