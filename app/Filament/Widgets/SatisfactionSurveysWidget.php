<?php

namespace App\Filament\Widgets;

use App\Models\TicketSatisfactionSurvey;
use App\Services\Satisfaction\SatisfactionSurveyService;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class SatisfactionSurveysWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.satisfaction-surveys';

    protected ?string $pollingInterval = '30s';

    /**
     * @return array{score: float, formatted: string, total_surveys: int, breakdown: array<string, float>}
     */
    public function getMetrics(): array
    {
        return app(SatisfactionSurveyService::class)->getSatisfactionIndex();
    }

    /**
     * @return Collection<int, TicketSatisfactionSurvey>
     */
    public function getRecentSurveys(): Collection
    {
        return TicketSatisfactionSurvey::query()
            ->with(['ticket', 'user'])
            ->latest()
            ->take(5)
            ->get();
    }
}
