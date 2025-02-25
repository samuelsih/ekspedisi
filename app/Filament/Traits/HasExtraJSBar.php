<?php

namespace App\Filament\Traits;

use Filament\Support\RawJs;

trait HasExtraJSBar
{
    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            xaxis: {
                labels: {
                    formatter: (value) => value.length > 5 ? value.slice(0, 5) + '...' : value
                }
            },

            tooltip: {
                y: {
                    formatter: function(value, { dataPointIndex, w }) {
                        return w.globals.labels[dataPointIndex] + ': ' + value;
                    }
                }
            }
        }
        JS);
    }
}
