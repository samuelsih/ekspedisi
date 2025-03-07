<?php

namespace App\Http\Controllers;

use App\Http\Requests\AntiSurveyRequest;
use App\Models\Customer;
use App\Models\CustomerSurveyDecline;
use App\Models\CustomerSurveyDeclineAnswer;
use App\Models\Feature;
use App\Models\WebConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AntiSurveyController extends Controller
{
    public function __construct()
    {
        throw_unless(Feature::active('customer-survey-decline'), NotFoundHttpException::class);
    }

    public function index()
    {
        $title = WebConfig::query()
            ->where('name', 'Judul halaman penolakan survey')
            ->first('value')['value'];

        $subtitle = WebConfig::query()
            ->where('name', 'Sub judul halaman penolakan survey')
            ->first('value')['value'];

        $answers = CustomerSurveyDeclineAnswer::query()
            ->get(['id', 'answer']);

        return Inertia::render('AntiSurvey', [
            'title' => $title,
            'subtitle' => $subtitle,
            'answers' => $answers,
        ]);
    }

    public function store(AntiSurveyRequest $request)
    {
        $validated = $request->validated();
        Log::debug('ini request', $validated);

        try {
            DB::beginTransaction();

            $customer = Customer::query()
                ->where('id', $validated['customerId'])
                ->orWhere('id_customer', $validated['customerId'])
                ->first();

            if (! $customer) {
                $customer = Customer::create(['id_customer' => $validated['customerId']]);
            }

            CustomerSurveyDecline::query()->create([
                'customer_id' => $customer->id,
                'driver_id' => $validated['driverId'],
                'channel_id' => $validated['channelId'],
                'customer_survey_decline_answer_id' => $validated['answerId'],
            ]);

            DB::commit();
        } catch (Exception) {
            DB::rollBack();

            return response()->json(['message' => 'Gagal menyimpan data. Coba beberapa saat lagi'], 500);
        }

        return response()->json(['message' => 'OK'], 201);
    }
}
