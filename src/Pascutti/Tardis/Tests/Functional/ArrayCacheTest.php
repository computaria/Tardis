<?php

use Pascutti\Tardis;
use Pascutti\Tardis\Tests\Fixture;
use Doctrine\Common\Cache;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory as ProxyFactory;

/**
 * @TODO Refactor Inroduce Parameter Object for Tardis\Factory configuration.
 */
class ArrayCacheTest extends \PHPUnit_Framework_TestCase
{
    const EXISTING_CACHE_KEY = 1;
    const EXISTING_CACHE_VALUE = '1424198185';
    const MISSING_CACHE_KEY = 2;
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cacheAdapter = null;
    /**
     * @var \ProxyManager\Factory\AbstractBaseFactory
     */
    private $proxyFactory = null;
    /**
     * @var \Pascutti\Tardis\Tests\Fixture\Doctor
     */
    /**
     * @var Pascutti\Tardis\Tests\Fixture\Doctor
     */
    private $subject = null;

    public function setUp()
    {
        $this->subject = new Fixture\Doctor('Who', 'Allonzy');

        $this->proxyFactory = new ProxyFactory;
        $this->cacheAdapter = new Cache\ArrayCache();
        $this->cacheAdapter->save(
            self::EXISTING_CACHE_KEY,
            self::EXISTING_CACHE_VALUE
        );
    }

    /**
     * @test
     */
    public function fetches_cache_for_existing_key()
    {
        $idGenerator = new Tardis\Identity\AlwaysTheSameValue(self::EXISTING_CACHE_KEY);
        $tardis = new Tardis\Factory($this->proxyFactory, $this->cacheAdapter, $idGenerator);

        $proxiedSubject = $tardis->cacheCallsFrom($this->subject);

        $this->assertInstanceOf(
            get_class($this->subject),
            $proxiedSubject,
            'The cache-able object should have the same type as the real subject class.'
        );
        $this->assertEquals(
            $expectedResult = self::EXISTING_CACHE_VALUE,
            $proxiedSubject->salute(),
            'Cached result of a method should return cache content.'
        );
    }

    /**
     * @test
     */
    public function unexisting_cache_creates_cache_entry()
    {
        $idGenerator = new Tardis\Identity\AlwaysTheSameValue(self::MISSING_CACHE_KEY);
        $tardis = new Tardis\Factory($this->proxyFactory, $this->cacheAdapter, $idGenerator);

        $proxiedSubject = $tardis->cacheCallsFrom($this->subject);
        $this->assertFalse(
            $this->cacheAdapter->contains(self::MISSING_CACHE_KEY),
            'Cache should not contain missing key.'
        );
        $this->assertInstanceOf(
            get_class($this->subject),
            $proxiedSubject,
            'The cache-able object should have the same type as the real subject class.'
        );

        $proxiedSubject->salute();

        $this->assertTrue(
            $this->cacheAdapter->contains(self::MISSING_CACHE_KEY),
            'Cache should contain missing key after method is called on proxy.'
        );
        $this->assertEquals(
            $this->subject->salute(),
            $this->cacheAdapter->fetch(self::MISSING_CACHE_KEY),
            'Value put on cache should be the same one returned by the real subject.'
        );
    }
}
