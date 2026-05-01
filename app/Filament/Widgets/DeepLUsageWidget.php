<?php

namespace App\Filament\Widgets;

use App\Services\Raiida\DeepLTranslationService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class DeepLUsageWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $service = app(DeepLTranslationService::class);
        $usage = $service->getUsage();

        if (! $usage) {
            return [
                Stat::make('DeepL Translation API', 'Not Configured or Error')
                    ->description('Please check your DEEPL_API_KEY')
                    ->color('danger'),
            ];
        }

        $count = $usage['character_count'] ?? 0;
        $limit = max(1, (int) ($usage['character_limit'] ?? 1));
        
        $percentage = min(100, round(($count / $limit) * 100, 1));
        
        $color = 'success';
        if ($percentage > 80) $color = 'warning';
        if ($percentage > 95) $color = 'danger';

        $formattedCount = Number::format($count);
        $formattedLimit = Number::format($limit);

        return [
            Stat::make('DeepL Translation Usage', "{$formattedCount} / {$formattedLimit} chars")
                ->description("{$percentage}% of current monthly limit used")
                ->color($color)
                ->chart([$count]) // small visual
        ];
    }
}
