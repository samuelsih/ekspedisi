<?php

namespace App\Filament\Widgets;

use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class TopContributionDriver extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top 10 Contribution Driver';

    protected function getData(): array
    {
        $start = $this->filters['startDate'];
        $end = $this->filters['endDate'];
        $channelId = $this->filters['channelId'];

        $stmt =
            "
            WITH surveys_count AS (
                SELECT driver_id, COUNT(*) as s_count
                    FROM surveys
                    WHERE (NULLIF(:startDate, '') IS NULL OR DATE(created_at) >= NULLIF(:startDate, ''))
                    AND (NULLIF(:endDate, '') IS NULL OR DATE(created_at) <= NULLIF(:endDate, ''))
                    AND (NULLIF(:channelId, '') IS NULL OR channel_id = NULLIF(:channelId, ''))
                    GROUP by driver_id
                ),
                customer_survey_declines_count AS (
                    SELECT driver_id, COUNT(*) AS s_count
                    FROM customer_survey_declines
                    WHERE (NULLIF(:startDate, '') IS NULL OR DATE(created_at) >= NULLIF(:startDate, ''))
                    AND (NULLIF(:endDate, '') IS NULL OR DATE(created_at) <= NULLIF(:endDate, ''))
                    AND (NULLIF(:channelId, '') IS NULL OR channel_id = NULLIF(:channelId, ''))
                    GROUP by driver_id
                ),
                survey_answers_avg_rating AS (
                    SELECT s.driver_id AS driver_id, COALESCE(AVG(sa.value), 0) as avg_rating
                    FROM survey_answers sa
                    JOIN surveys s
                    ON sa.survey_id = s.id
                    WHERE (NULLIF(:startDate, '') IS NULL OR DATE(s.created_at) >= NULLIF(:startDate, ''))
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
                ORDER by driver_contribution DESC
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
            'datasets' => [
                [
                    'label' => 'Poin',
                    'data' => $data->pluck('driver_contribution')->toArray(),
                ],
            ],
            'labels' => $data->map(fn ($customer) => "{$customer->name}")->toArray(),
        ];
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<'JS'
            {
                scales: {
                    x: {
                        ticks: {
                            callback: function(value, index, ticks) {
                                const limit = 5;
                                const v = this.getLabelForValue(value);

                                if (v.length > limit) return v.slice(0, limit) + '...';
                                return v;
                            }
                        }
                    }
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
