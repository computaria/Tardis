<?php

namespace Computaria\Tardis\Proxy;

use Computaria\Tardis\Tests\Fixture;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory as ProxyFactory;

/**
 * @group wip
 */
class PublicMethodsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ProxyManager\Factory\AbstractBaseFactory
     */
    private $proxyFactory = null;
    /**
     * @var \Computaria\Tardis\Interceptor\PrefixInterceptor
     */
    private $prefixInterceptor = null;
    /**
     * @var \Computaria\Tardis\Interceptor\SufixInterceptor
     */
    private $sufixIntercetor = null;
    /**
     * @var \Computaria\Tardis\Identity\IdentityGenerator
     */
    private $identityGenerator = null;
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cacheAdapter = null;
    /**
     * @var \Computaria\Tardis\Proxy\PublicMethods
     */
    private $factory = null;

    public function setUp()
    {
        $this->proxyFactory = $this->getMockForAbstractClass(
            'ProxyManager\Factory\AbstractBaseFactory',
            $constructorArguments = [],
            $className = '',
            $callOriginalConstructor = true,
            $callOriginalClone = true,
            $useAutoload = true,
            ['createProxy']
        );
        $this->prefixInterceptor = $this->getMock('Computaria\\Tardis\\Interceptor\\PrefixInterceptor');
        $this->sufixIntercetor = $this->getMock('Computaria\\Tardis\\Interceptor\\SufixInterceptor');
        $this->identityGenerator = $this->getMock('Computaria\\Tardis\\Identity\\IdentityGenerator');
        $this->cacheAdapter = $this->getMock('Doctrine\\Common\\Cache\\Cache');

        $this->factory = new PublicMethods(
            $this->proxyFactory,
            $this->prefixInterceptor,
            $this->sufixIntercetor,
            $this->identityGenerator,
            $this->cacheAdapter
        );
    }

    /**
     * @test
     */
    public function can_create_proxy_from_instances()
    {
        $realInstance = new Fixture\TimeLord('Rassilion', 'I\'m the first and only');

        $this->proxyFactory->expects($this->once())
            ->method('createProxy')
            ->with(
                $realInstance,
                $this->contains($this->prefixInterceptor),
                $this->contains($this->sufixIntercetor)
            );

        $proxiedInstance = $this->factory->cacheCallsFrom($realInstance);
    }

    /**
     * @test
     */
    public function can_invalidate_cache()
    {
        $this->cacheAdapter->expects($this->once())
            ->method('delete');

        $this->factory->invalidate('salute', []);
    }
}
