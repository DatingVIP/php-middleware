<?php

namespace DatingVIP\Middleware;

use DatingVIP\Middleware\Request\Stamp\StampInterface;

interface RequestInterface
{
    public function getRequest();
    public function with(StampInterface ...$stamps): RequestInterface;
    public function withoutAll(string $stampFqcn): RequestInterface;
    public function withoutStampsOfType(string $type): self;
    public function all(string $stampFqcn = null): \ArrayObject;
}