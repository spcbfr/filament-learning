<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;

class PatientTypeOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $eloquentData = Patient::query()->where('type', 'rabbit');
        $data = Trend::query($eloquentData)
            ->between(
                start: now()->startOfWeek(),
                end: now()
            )
            ->perDay()
            ->count();
        $diff = $data->last()->aggregate - $data->first()->aggregate;
        $properColor = match (true) {
            $diff > 0 => 'success',
            $diff < 0 => 'danger',
            $diff == 0 => 'warning'
        };

        return [
            Stat::make('Cats', $eloquentData->count()),
            Stat::make('Dogs', Patient::query()->where('type', 'dog')->count()),
            Stat::make('Rabbits', Patient::query()->where('type', 'rabbit')->count())
                ->chart($data->map(fn ($value) => $value->aggregate)->all())
                ->color($properColor),
        ];
    }
}
