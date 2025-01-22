<?php


namespace App\Shared\Domain\Collection;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

abstract class Collection implements Countable, IteratorAggregate
{
    protected array $items;

    protected function __construct(array $items = [])
    {
        $this->items = $items;
    }

    abstract protected function type(): string;

    protected function guardType(object $item): void
    {
        $type = $this->type();

        if (!$item instanceof $type) {
            throw new \InvalidArgumentException(
                sprintf('Item must be instance of %s, %s given', $type, get_class($item))
            );
        }
    }

    public function add(object $item): static
    {
        $this->guardType($item);

        return new static([...$this->items, $item]);
    }

    public function filter(callable $callback): static
    {
        return new static(array_filter($this->items, $callback));
    }

    public function map(callable $callback): array
    {
        return array_map($callback, $this->items);
    }

    public function first(): ?object
    {
        return $this->items[0] ?? null;
    }

    public function last(): ?object
    {
        return !empty($this->items) ? end($this->items) : null;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}