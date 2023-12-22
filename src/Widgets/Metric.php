<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

abstract class Metric extends Widget
{
    /**
     * The Eloquent query.
     */
    protected ?Builder $query = null;

    /**
     * The query resolver callback.
     */
    protected ?Closure $queryResolver = null;

    /**
     * Calculate the metric data.
     */
    abstract public function calculate(Request $request): array;

    /**
     * Set the query.
     */
    public function setQuery(Builder $query): static
    {
        $this->query = $query->clone()->withoutEagerLoads();

        return $this;
    }

    /**
     * Set the query resolver.
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the query.
     */
    public function resolveQuery(Request $request): Builder
    {
        if (is_null($this->query)) {
            throw new QueryResolutionException();
        }

        return is_null($this->queryResolver)
            ? $this->query
            : call_user_func_array($this->queryResolver, [$request, $this->query]);
    }

    /**
     * Get the to date.
     */
    public function to(Request $request): DateTimeInterface
    {
        return Date::now();
    }

    /**
     * Get the from date.
     */
    public function from(Request $request): DateTimeInterface
    {
        $to = $this->to($request);

        $range = empty($this->ranges()) ? 'ALL' : $this->getCurrentRange($request);

        return $this->range($to, $range);
    }

    /**
     * Get the current range.
     */
    public function getCurrentRange(Request $request): string
    {
        return $request->input('range', 'MONTH');
    }

    /**
     * Create a new method.
     */
    protected function range(DateTimeInterface $date, string $range): mixed
    {
        return match ($range) {
            'TODAY' => $date->startOfDay(),
            'WEEK' => $date->subWeek(),
            'MONTH' => $date->subMonth(),
            'QUARTER' => $date->subQuarter(),
            'YEAR' => $date->subYear(),
            'ALL' => Date::parse('0000-01-01'),
            default => $date->subDays((int) $range),
        };
    }

    /**
     * Get the available ranges.
     */
    public function ranges(): array
    {
        return [
            'TODAY' => __('Today'),
            'WEEK' => __('Week to today'),
            'MONTH' => __('Month to today'),
            'QUARTER' => __('Quarter to today'),
            'YEAR' => __('Year to today'),
            'ALL' => __('All time'),
        ];
    }

    /**
     * Get the data.
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'ranges' => $this->ranges(),
            'data' => $this->calculate($request),
        ]);
    }
}
