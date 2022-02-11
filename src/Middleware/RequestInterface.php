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
    public function stampExists(string $stampFqcn) : bool;
    public function getStamp(string $stampFqcn);
    public function setStamp(StampInterface $stamp);
    public function setStampIfNotExist(string $stampFqcn);
}