<?php

namespace DatingVIP\Middleware;


use DatingVIP\Middleware\Request\Stamp\StampInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class Request implements RequestInterface
{
    private \ArrayObject $stamps;
    private $request;

    /**
     * @param mixed            $message
     * @param StampInterface[] $stamps
     */
    public function __construct($request, array $stamps = [])
    {
        $this->stamps = new \ArrayObject();

        $this->request = $request;

        foreach ($stamps as $stamp) {
            $index = \get_class($stamp);

            if (!$this->stamps->offsetExists($index)) {
                $this->stamps->offsetSet($index, new \ArrayObject());
            }
            $this->stamps->offsetGet($index)->append($stamp);
        }
    }

    /**
     * @param object|Request  $message
     * @param StampInterface[] $stamps
     */
    public static function wrap($request, array $stamps = []): self
    {
        $envelope = $request instanceof self ? $request : new self($request);

        return $envelope->with(...$stamps);
    }

    /**
     * @return Request a new Envelope instance with additional stamp
     */
    public function with(StampInterface ...$stamps): self
    {
        $cloned = clone $this;

        foreach ($stamps as $stamp) {
            $index = \get_class($stamp);

            if (!$cloned->stamps->offsetExists($index)) {
                $cloned->stamps->offsetSet($index, new \ArrayObject());
            }

            $cloned->stamps->offsetGet($index)->append($stamp);
        }

        return $cloned;
    }

    /**
     * @return Request a new Envelope instance without any stamps of the given class
     */
    public function withoutAll(string $stampFqcn): self
    {
        $cloned = clone $this;

        unset($cloned->stamps[$this->resolveAlias($stampFqcn)]);

        return $cloned;
    }

    /**
     * Removes all stamps that implement the given type.
     */
    public function withoutStampsOfType(string $type): self
    {
        $cloned = clone $this;
        $type = $this->resolveAlias($type);

        foreach ($cloned->stamps as $class => $stamps) {
            if ($class === $type || is_subclass_of($class, $type)) {
                unset($cloned->stamps[$class]);
            }
        }

        return $cloned;
    }

    public function last(string $stampFqcn): ?StampInterface
    {
        return isset($this->stamps[$stampFqcn = $this->resolveAlias($stampFqcn)]) ? end($this->stamps[$stampFqcn]) : null;
    }

    /**
     * @return StampInterface[]|StampInterface[][] The stamps for the specified FQCN, or all stamps by their class name
     */
    public function all(string $stampFqcn = null): \ArrayObject
    {
        if (null !== $stampFqcn) {
            return $this->stamps[$this->resolveAlias($stampFqcn)] ?? new \ArrayObject();
        }

        return $this->stamps;
    }

    /**
     * @return object The original message contained in the envelope
     */
    public function getRequest()
    {
        return $this->request;
    }

    private function resolveAlias(string $fqcn): string
    {
        static $resolved;

        return $resolved[$fqcn] ?? ($resolved[$fqcn] = (new \ReflectionClass($fqcn))->getName());
    }

    public function stampExists(string $stampFqcn) : bool
    {
        $stamps = $this->all($stampFqcn);

        return $stamps->count() > 0;
    }

    public function getStamp(string $stampFqcn)
    {
        $stamps = $this->all($stampFqcn);

        if ($stamps->count() == 0) {
            throw new \RuntimeException(sprintf("%s has not been found in the passed request. Current middleware must be executed after a middleware which set %s in the request.", $stampFqcn, $stampFqcn));
        }

        return $stamps->offsetGet($stamps->count() - 1);
    }

    public function setStamp(StampInterface $stamp)
    {
        return $this->with($stamp);
    }

    public function setStampIfNotExist(string $stampFqcn)
    {
        if ($this->stampExists($stampFqcn)) {
            return $this;
        }
        return $this->with(new $stampFqcn([]));
    }
}
