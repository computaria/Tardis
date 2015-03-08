<?php

namespace Computaria\Tardis\Interceptor;

/**
 * Actions to be called before the method call on the proxied object
 * should implement this interface.
 */
interface PrefixInterceptor
{
    /**
    * @var object $proxy            The proxy that intercepted the method call
    * @var object $instance         The wrapped instance within the proxy
    * @var string $methodName       Name of the called method
    * @var array  $methodArguments  Sorted array of parameters passed to the intercepted
    *                               method, indexed by parameter name
    * @var bool   $returnEarly      Flag to tell the interceptor proxy to return early, returning
    *                               the interceptor's return value instead of executing the method logic
    * @return mixed
    */
    public function __invoke($proxy, $instance, $methodName, $methodArguments, &$returnEarly);
}
