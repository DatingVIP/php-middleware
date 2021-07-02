<?php

namespace DatingVIP\Middleware;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
interface MiddlewareStackInterface
{
    /**
     * Returns the next middleware to process a message.
     */
    public function next(): MiddlewareInterface;
}
