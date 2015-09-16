<?php

namespace Computaria\Tardis;

use InvalidArgumentException;
use Doctrine\Common\Cache\Cache;
use ProxyManager\Factory as ProxyManager;
use ProxyManager\Configuration as ProxyConfiguration;
use Computaria\Tardis\Identity\IdentityGenerator;

class Factory
{
    public static function grow(
        Cache $cacheAdapter,
        IdentityGenerator $idGenerator = null,
        ProxyManager\AbstractBaseFactory $proxyFactory = null
    ) {
        $idGenerator = self::createIdentityGeneratorIfNecessary($idGenerator);
        $proxyFactory = self::createProxyFactoryIfNecessary($proxyFactory);

        $beforeMethodCall = new Interceptor\CacheFetch($cacheAdapter, $idGenerator);
        $afterMethodCall = new Interceptor\CacheSave($cacheAdapter, $idGenerator);

        return new Proxy\PublicMethods($proxyFactory, $beforeMethodCall, $afterMethodCall);
    }

    private static function createProxyFactoryIfNecessary(ProxyManager\AbstractBaseFactory $proxyFactory=null)
    {
        if (!is_null($proxyFactory)) {
            return $proxyFactory;
        }

        $proxyDirectory = sys_get_temp_dir();
        $proxyConfiguration = new ProxyConfiguration();
        $proxyConfiguration->setProxiesTargetDir($proxyDirectory);
        spl_autoload_register($proxyConfiguration->getProxyAutoloader());

        return new ProxyManager\AccessInterceptorValueHolderFactory($proxyConfiguration);
    }

    private static function createIdentityGeneratorIfNecessary(IdentityGenerator $idGenerator=null)
    {
        if (!is_null($idGenerator)) {
            return $idGenerator;
        }

        return new Identity\MethodCall;
    }
}
