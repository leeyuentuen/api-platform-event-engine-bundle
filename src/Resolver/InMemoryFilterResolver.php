<?php

declare(strict_types=1);

namespace ADS\Bundle\ApiPlatformEventEngineBundle\Resolver;

use ADS\Bundle\ApiPlatformEventEngineBundle\Filter\InMemoryFilterConverter;
use ApiPlatform\State\Pagination\ArrayPaginator;
use Closure;
use Traversable;

use function count;
use function iterator_to_array;

final class InMemoryFilterResolver extends FilterResolver
{
    /** @var array<mixed> */
    private array $collection;

    public function __construct(InMemoryFilterConverter $filterConverter)
    {
        $this->filterConverter = $filterConverter;
    }

    /**
     * @param Traversable<mixed>|array<mixed> $collection
     */
    public function setCollection(Traversable|array $collection): static
    {
        if ($collection instanceof Traversable) {
            $collection = iterator_to_array($collection);
        }

        $this->collection = $collection;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function collection(): array
    {
        $collection = $this->collection;
        /** @var Closure|null $filter */
        $filter = $this->filter();
        /** @var Closure|null $order */
        $order = $this->orderBy();

        if ($filter instanceof Closure) {
            $collection = ($filter)($collection);
        }

        if ($order instanceof Closure) {
            $collection = ($order)($collection);
        }

        return $collection;
    }

    /**
     * @inheritDoc
     */
    protected function totalItems(array $collection): int
    {
        return count($collection);
    }

    /**
     * @inheritDoc
     */
    protected function result(array $collection, int $page, int $itemsPerPage, int $totalItems): mixed
    {
        return new ArrayPaginator(
            $collection,
            ($page - 1) * $itemsPerPage,
            $itemsPerPage,
        );
    }
}
