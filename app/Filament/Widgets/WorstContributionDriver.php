<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\HasExtraJSBar;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class WorstContributionDriver extends ApexChartWidget
{
    use HasExtraJSBar, InteractsWithPageFilters;

    protected static ?string $heading = 'Worst Rating by Contribution';

    protected function getOptions(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];
        $channelId = $this->filters['channelId'];

        $stmt =
            "
            WITH surveys_count AS (
                SELECT driver_id, COUNT(*) as s_count
                    FROM surveys
                    WHERE deleted_at IS NULL
                    AND (NULLIF(:startDate, '') IS NULL OR DATE(created_at) >= NULLIF(:startDate, ''))
                    AND (NULLIF(:endDate, '') IS NULL OR DATE(created_at) <= NULLIF(:endDate, ''))
                    AND (NULLIF(:channelId, '') IS NULL OR channel_id = NULLIF(:channelId, ''))
                    GROUP by driver_id
                ),
                customer_survey_declines_count AS (
                    SELECT driver_id, COUNT(*) AS s_count
                    FROM customer_survey_declines
                    WHERE deleted_at IS NULL
                    AND (NULLIF(:startDate, '') IS NULL OR DATE(created_at) >= NULLIF(:startDate, ''))
                    AND (NULLIF(:endDate, '') IS NULL OR DATE(created_at) <= NULLIF(:endDate, ''))
                    AND (NULLIF(:channelId, '') IS NULL OR channel_id = NULLIF(:channelId, ''))
                    GROUP by driver_id
                ),
                survey_answers_avg_rating AS (
                    SELECT s.driver_id AS driver_id, COALESCE(AVG(sa.value), 0) as avg_rating
                    FROM survey_answers sa
                    JOIN surveys s
                    ON sa.survey_id = s.id
                    WHERE s.deleted_at IS NULL
                    AND (NULLIF(:startDate, '') IS NULL OR DATE(s.created_at) >= NULLIF(:startDate, ''))
                    AND (NULLIF(:endDate, '') IS NULL OR DATE(s.created_at) <= NULLIF(:endDate, ''))
                    AND (NULLIF(:channelId, '') IS NULL OR s.channel_id = NULLIF(:channelId, ''))
                    GROUP BY s.driver_id
                )
            SELECT
                d.nik, d.name,
                CASE WHEN (sc.s_count + COALESCE(csd.s_count, 0)) = 0 THEN 0
                ELSE (1.0 * sc.s_count / (sc.s_count + COALESCE(csd.s_count, 0))) * sa.avg_rating
                END AS driver_contribution
            FROM drivers d
            JOIN surveys_count sc ON d.id = sc.driver_id
            LEFT JOIN customer_survey_declines_count csd on d.id = csd.driver_id
            LEFT JOIN survey_answers_avg_rating sa ON d.id = sa.driver_id
            WHERE d.deleted_at IS NULL
            ORDER by driver_contribution ASC
            LIMIT 10
            ;
            ";

        $result = DB::select($stmt, [
            'startDate' => $start,
            'endDate' => $end,
            'channelId' => $channelId,
        ]);

        $data = collect($result);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => '',
                    'data' => $data->pluck('driver_contribution')->map(fn ($v) => round($v, 2))->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn ($driver) => "{$driver->name}")->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }
}
