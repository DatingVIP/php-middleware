<?php

namespace DatingVIP\Middleware;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
interface MiddlewareInterface
{
    public function handle(RequestInterface $request, ?MiddlewareStackInterface $stack = null): RequestInterface;
}
