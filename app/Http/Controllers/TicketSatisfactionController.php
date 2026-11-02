<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSatisfactionSurveyRequest;
use App\Models\Ticket;
use App\Services\Satisfaction\SatisfactionSurveyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class TicketSatisfactionController extends Controller
{
    public function __construct(
        protected SatisfactionSurveyService $surveyService
    ) {}

    public function store(StoreSatisfactionSurveyRequest $request, Ticket $ticket): RedirectResponse|JsonResponse
    {
        $survey = $this->surveyService->submitSurvey(
            $ticket,
            $request->user(),
            $request->validated()
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas penilaian dan ulasan layanan yang Anda berikan.',
                'survey' => $survey,
            ]);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Terima kasih atas penilaian dan ulasan layanan yang Anda berikan.');
    }
}
