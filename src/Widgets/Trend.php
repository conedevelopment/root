<?php

namespace Cone\Root\Widgets;

use Closure;
use DatePeriod;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

abstract class Trend extends Metric
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::widgets.trend';

    /**
     * The trend chart config.
     */
    protected array $config = [];

    /**
     * Create a new trend chart instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->config = Config::get('root.widgets.trend', []);
    }

    /**
     * Set the configuration.
     */
    public function withConfig(Closure $callback): static
    {
        $this->config = call_user_func_array($callback, [$this->config]);

        return $this;
    }

    /**
     * Get the widget configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function periodFromRequest(Request $request, string $interval = 'day'): DatePeriod
    {
        return $this->period(
            $this->rangeToDateTime($this->getCurrentRange($request)),
            'now',
            $this->duration($interval)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function count(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->countByDays($request, $query, $column, $dateColumn);
    }

    /**
     * Count by minutes.
     */
    protected function countByMinutes(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'count', $column, $dateColumn, 'minute');
    }

    /**
     * Count by hours.
     */
    protected function countByHours(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'count', $column, $dateColumn, 'hour');
    }

    /**
     * Count by days.
     */
    protected function countByDays(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'count', $column, $dateColumn, 'day');
    }

    /**
     * Count by months.
     */
    protected function countByMonths(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'count', $column, $dateColumn, 'month');
    }

    /**
     * Count by years.
     */
    protected function countByYears(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'count', $column, $dateColumn, 'year');
    }

    /**
     * {@inheritdoc}
     */
    protected function avg(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->avgByDays($request, $query, $column, $dateColumn);
    }

    /**
     * Average by minutes.
     */
    protected function avgByMinutes(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'avg', $column, $dateColumn, 'minute');
    }

    /**
     * Average by hours.
     */
    protected function avgByHours(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'avg', $column, $dateColumn, 'hour');
    }

    /**
     * Average by days.
     */
    protected function avgByDays(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'avg', $column, $dateColumn, 'day');
    }

    /**
     * Average by months.
     */
    protected function avgByMonths(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'avg', $column, $dateColumn, 'month');
    }

    /**
     * Average by years.
     */
    protected function avgByYears(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'avg', $column, $dateColumn, 'year');
    }

    /**
     * {@inheritdoc}
     */
    protected function sum(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->sumByDays($request, $query, $column, $dateColumn);
    }

    /**
     * Sum by minutes.
     */
    protected function sumByMinutes(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'sum', $column, $dateColumn, 'minute');
    }

    /**
     * Sum by hours.
     */
    protected function sumByHours(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'sum', $column, $dateColumn, 'hour');
    }

    /**
     * Sum by days.
     */
    protected function sumByDays(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'sum', $column, $dateColumn, 'day');
    }

    /**
     * Sum by months.
     */
    protected function sumByMonths(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'sum', $column, $dateColumn, 'month');
    }

    /**
     * Sum by years.
     */
    protected function sumByYears(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'sum', $column, $dateColumn, 'year');
    }

    /**
     * {@inheritdoc}
     */
    protected function min(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->minByDays($request, $query, $column, $dateColumn);
    }

    /**
     * Min by minutes.
     */
    protected function minByMinutes(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'min', $column, $dateColumn, 'minute');
    }

    /**
     * Min by hours.
     */
    protected function minByHours(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'min', $column, $dateColumn, 'hour');
    }

    /**
     * Min by days.
     */
    protected function minByDays(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'min', $column, $dateColumn, 'day');
    }

    /**
     * Min by months.
     */
    protected function minByMonths(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'min', $column, $dateColumn, 'month');
    }

    /**
     * Min by years.
     */
    protected function minByYears(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'min', $column, $dateColumn, 'year');
    }

    /**
     * {@inheritdoc}
     */
    protected function max(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->maxByDays($request, $query, $column, $dateColumn);
    }

    /**
     * Max by minutes.
     */
    protected function maxByMinutes(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'max', $column, $dateColumn, 'minute');
    }

    /**
     * Max by hours.
     */
    protected function maxByHours(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'max', $column, $dateColumn, 'hour');
    }

    /**
     * Max by days.
     */
    protected function maxByDays(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'max', $column, $dateColumn, 'day');
    }

    /**
     * Max by months.
     */
    protected function maxByMonths(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'max', $column, $dateColumn, 'month');
    }

    /**
     * Max by years.
     */
    protected function maxByYears(Request $request, Builder $query, string $column = '*', ?string $dateColumn = null): array
    {
        return $this->aggregateBy($request, $query, 'max', $column, $dateColumn, 'year');
    }

    /**
     * Aggregate by the given interval.
     */
    protected function aggregateBy(Request $request, Builder $query, string $fn, string $column, ?string $dateColumn = null, string $interval = 'day'): array
    {
        $period = $this->periodFromRequest($request, $interval);

        $result = $this->result(
            $this->aggregate($query, $period, $fn, $column, $dateColumn, $interval)
        );

        return $this->resultBy($result, $period, $interval);
    }

    /**
     * {@inheritdoc}
     */
    protected function aggregate(Builder $query, DatePeriod $period, string $fn, string $column, ?string $dateColumn = null, string $interval = 'day'): Builder
    {
        $dateColumn ??= $query->getModel()->getCreatedAtColumn();

        $wrappedColumn = $query->getQuery()->getGrammar()->wrap($query->qualifyColumn($dateColumn));

        $format = match ($query->getQuery()->getConnection()->getDriverName()) {
            'mysql' => $this->mySqlFormat($wrappedColumn, $interval),
            'sqlite' => $this->sqliteFormat($wrappedColumn, $interval),
            'pgsql' => $this->pgsqlFormat($wrappedColumn, $interval),
            'sqlsrv' => $this->sqliteFormat($wrappedColumn, $interval),
            default => throw new Exception('Unsupported database driver.'),
        };

        return parent::aggregate($query, $period, $fn, $column, $dateColumn)
            ->selectRaw(sprintf('%s as `__interval`', $format))
            ->groupBy('__interval')
            ->orderBy('__interval');
    }

    /**
     * Get the duration by the given interval.
     */
    public function duration(string $interval): string
    {
        return match ($interval) {
            'minute' => 'PT1M',
            'hour' => 'PT1H',
            'day' => 'P1D',
            'month' => 'P1M',
            'year' => 'P1Y',
            default => 'P1D',
        };
    }

    /**
     * {@inheritdoc}
     */
    public function ranges(): array
    {
        return [
            'TODAY' => __('Today'),
            'WEEK' => __('Week to today'),
            'MONTH' => __('Month to today'),
            'QUARTER' => __('Quarter to today'),
            'YEAR' => __('Year to today'),
        ];
    }

    /**
     * Format the interval for MySQL.
     */
    protected function mySqlFormat(string $column, string $interval): string
    {
        $format = match ($interval) {
            'minute' => '%Y-%m-%d %H:%i:00',
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => throw new InvalidArgumentException('Invalid interval for MySQL.'),
        };

        return sprintf("date_format(%s, '%s')", $column, $format);
    }

    /**
     * Format the interval for SQLite.
     */
    protected function sqliteFormat(string $column, string $interval): string
    {
        $format = match ($interval) {
            'minute' => '%Y-%m-%d %H:%M:00',
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => throw new InvalidArgumentException('Invalid interval for SQLite.'),
        };

        return sprintf("strftime('%s', %s)", $format, $column);
    }

    /**
     * Format the interval for PgSQL.
     */
    protected function pgsqlFormat(string $column, string $interval): string
    {
        $format = match ($interval) {
            'minute' => 'YYYY-MM-DD HH24:MI:00',
            'hour' => 'YYYY-MM-DD HH24:00:00',
            'day' => 'YYYY-MM-DD',
            'month' => 'YYYY-MM',
            'year' => 'YYYY',
            default => throw new InvalidArgumentException('Invalid interval for PgSQL'),
        };

        return sprintf("to_char(%s, '%s')", $column, $format);
    }

    /**
     * Format the interval for SQLServer.
     */
    protected function sqlserverFormat(string $column, string $interval): string
    {
        $format = match ($interval) {
            'minute' => 'yyyy-MM-dd HH:mm:00:00',
            'hour' => 'yyyy-MM-dd HH:00:00',
            'day' => 'yyyy-MM-dd',
            'month' => 'yyyy-MM',
            'year' => 'yyyy',
            default => throw new InvalidArgumentException('Invalid interval for PgSQL'),
        };

        return sprintf("FORMAT(%s, '%s')", $column, $format);
    }

    /**
     * Format the interval for SQLServer.
     */
    protected function format(string $interval): string
    {
        return match ($interval) {
            'minute' => 'Y-m-d H:i:00',
            'hour' => 'Y-m-d H:00:00',
            'day' => 'Y-m-d',
            'month' => 'Y-m',
            'year' => 'Y',
            default => throw new InvalidArgumentException('Invalid interval.'),
        };
    }

    /**
     * Get the data.
     */
    public function data(Request $request): array
    {
        return array_replace_recursive([
            'data' => [
                'chart' => $request->isTurboFrameRequest() ? $this->config : [],
                'current' => null,
            ],
        ], parent::data($request));
    }

    /**
     * Get the results by the given interval.
     */
    public function resultBy(array $result, DatePeriod $period, string $interval): array
    {
        $dates = [];

        foreach ($period as $date) {
            $dates[$date->format($this->format($interval))] = 0;
        }

        $dates = array_replace($dates, $result);

        return [
            'current' => array_sum($dates),
            'chart' => [
                'series' => [
                    [
                        'name' => __('Value'),
                        'data' => array_values($dates),
                    ],
                ],
                'labels' => array_keys($dates),
            ],
        ];
    }
}
