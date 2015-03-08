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

        $this->factory = new PublicMethods(
            $this->proxyFactory,
            $this->prefixInterceptor,
            $this->sufixIntercetor
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

        $proxiedInstance = $this->factory->createProxy($realInstance);
    }
}
