<?php

namespace Computaria\Tardis\Proxy;

use Doctrine\Common\Cache\Cache;
use Computaria\Tardis\Identity\IdentityGenerator;
use ReflectionClass;
use ReflectionMethod;
use Computaria\Tardis\Interceptor;
use ProxyManager\Factory as ProxyFactory;

class PublicMethods
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
     * List of public methods to avoid proxy
     *
     * @var array
     */
    private $ignoreMethodsToProxy = [
        '__construct',
        '__destruct',
        '__clone',
        '__sleep',
        '__wakeUp',
        '__toString'
    ];

    public function __construct(
        ProxyFactory\AbstractBaseFactory $proxyFactory,
        Interceptor\PrefixInterceptor $prefixInterceptor,
        Interceptor\SufixInterceptor $sufixInterceptor,
        IdentityGenerator $identityGenerator,
        Cache $cacheAdapter
    ) {
        $this->proxyFactory = $proxyFactory;
        $this->prefixInterceptor = $prefixInterceptor;
        $this->sufixInterceptor = $sufixInterceptor;
        $this->identityGenerator = $identityGenerator;
        $this->cacheAdapter = $cacheAdapter;
    }

    public function cacheCallsFrom($instance)
    {
        $methodNames = $this->getAllPublicMethodsFromInstance($instance);
        $prefixInterceptors = array_map($this->apply($this->prefixInterceptor), $methodNames);
        $sufixInterceptors = array_map($this->apply($this->sufixInterceptor), $methodNames);

        return $this->proxyFactory->createProxy(
            $instance,
            $prefixInterceptors,
            $sufixInterceptors
        );
    }

    public function invalidate($methodName, array $methodArguments)
    {
        $cacheId = $this->identityGenerator->createIdFor($methodName, $methodArguments);
        return $this->cacheAdapter->delete($cacheId);
    }

    private function apply($interceptor)
    {
        return function ($methodName) use ($interceptor) {
            return $interceptor;
        };
    }

    /**
     * @TODO: Extract method to allow prior creation of proxy objects for production.
     */
    private function getAllPublicMethodsFromInstance($instance)
    {
        $class = new ReflectionClass($instance);
        $allPublicMethods = ReflectionMethod::IS_PUBLIC;
        $methodNames = [];

        foreach ($class->getMethods($allPublicMethods) as $method) {
            $methodName = $method->getName();
            if (in_array($methodName, $this->ignoreMethodsToProxy)) {
                continue;
            }

            $methodNames[$methodName] = $methodName;
        }

        return $methodNames;
    }
}
