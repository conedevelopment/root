<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Widgets\Results\TrendResult;
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
     * The interval.
     */
    protected string $interval = 'month';

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
    public function resolveQuery(Request $request): Builder
    {
        $query = parent::resolveQuery($request);

        $range = $this->getCurrentRange($request);

        $current = $this->currentPeriod($range);

        $column = $query->qualifyColumn($this->dateColumn);

        $wrappedColumn = $query->getQuery()->getGrammar()->wrap($column);

        $format = match ($query->getQuery()->getConnection()->getDriverName()) {
            'mysql' => $this->mySqlFormat($wrappedColumn, $this->interval),
            'sqlite' => $this->sqliteFormat($wrappedColumn, $this->interval),
            'pgsql' => $this->pgsqlFormat($wrappedColumn, $this->interval),
            'sqlsrv' => $this->sqliteFormat($wrappedColumn, $this->interval),
            default => throw new Exception('Unsupported database driver.'),
        };

        return $query
            ->selectRaw(sprintf("%s as '__interval'", $format))
            ->whereBetween($column, [
                $current->getStartDate()->format('Y-m-d H:i:s'),
                $current->getEndDate()->format('Y-m-d H:i:s'),
            ])
            ->groupBy('__interval')
            ->orderBy('__interval');
    }

    /**
     * {@inheritdoc}
     */
    public function duration(): string
    {
        return match ($this->interval) {
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
            'hour' => '%Y-%m-%d %H:00',
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
            'hour' => '%Y-%m-%d %H:00',
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
            'minute' => 'yyyy-MM-dd HH:mm',
            'hour' => 'yyyy-MM-dd HH',
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
            'minute' => 'Y-m-d h:i',
            'hour' => 'Y-m-dd h',
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
            'ranges' => $this->ranges(),
            'data' => [
                'chart' => $request->hasHeader('Turbo-Frame') ? $this->config : [],
                'value' => null,
            ],
        ], parent::data($request));
    }

    /**
     * Convert the query to result.
     */
    public function toResult(Request $request, Builder $query): TrendResult
    {
        $data = $query->getQuery()->get()->pluck('__value', '__interval')->all();

        $dates = [];

        foreach ($this->currentPeriod($this->getCurrentRange($request)) as $interval) {
            $dates[$interval->format($this->format($this->interval))] = 0;
        }

        return new TrendResult(
            array_replace($dates, $data)
        );
    }
}
