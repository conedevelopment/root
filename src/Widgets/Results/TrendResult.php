<?php

namespace Cone\Root\Widgets\Results;

class TrendResult extends Result
{
    /**
     * The trend data.
     */
    protected array $data = [];

    /**
     * Create a new trend result instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Convert the result as an array.
     */
    public function toArray(): array
    {
        return [
            'current' => array_sum($this->data),
            'chart' => [
                'series' => [
                    [
                        'name' => __('Value'),
                        'data' => array_values($this->data),
                    ],
                ],
                'labels' => array_keys($this->data),
            ],
        ];
    }
}
