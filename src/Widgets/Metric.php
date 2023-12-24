<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Widgets\Results\Result;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
    abstract public function calculate(Request $request): Result;

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
     * Get the current range.
     */
    public function getCurrentRange(Request $request): string
    {
        return $request->input('range', 'MONTH');
    }

    /**
     * Calculate the range.
     */
    protected function currentPeriod(string $range): DatePeriod
    {
        return new DatePeriod(
            (new DateTimeImmutable())->setTimestamp($this->rangeToTimestamp($range)),
            new DateInterval('P1D'),
            new DateTimeImmutable()
        );
    }

    /**
     * Convert the range to timestamp.
     */
    protected function rangeToTimestamp(string|int $range, ?int $base = null): int
    {
        return match ($range) {
            'TODAY' => strtotime('today', $base),
            'DAY' => strtotime('-1 day', $base),
            'WEEK' => strtotime('-1 week', $base),
            'MONTH' => strtotime('-1 month', $base),
            'QUARTER' => strtotime('-3 months', $base),
            'YEAR' => strtotime('-1 year', $base),
            'ALL' => 0,
            default => strtotime(sprintf('-%d days', (int) $range), $base),
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
            'data' => $this->calculate($request)->toArray(),
        ]);
    }
}
