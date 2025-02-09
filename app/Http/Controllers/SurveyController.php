<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyRequest;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Question;
use App\Models\Survey;
use App\Models\WebConfig;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SurveyController extends Controller
{
    public function index()
    {
        $questions = Question::query()->where('is_active', true)->get(['id', 'title']);
        $channels = Channel::all(['id', 'name']);

        $title = WebConfig::query()
            ->where('name', 'Judul halaman survey')
            ->first('value')['value'];

        $subtitle = WebConfig::query()
            ->where('name', 'Sub judul halaman survey')
            ->first('value')['value'];

        return Inertia::render('Survey', [
            'title' => $title,
            'subtitle' => $subtitle,
            'questions' => $questions,
            'channels' => $channels
        ]);
    }

    public function store(SurveyRequest $request)
    {
        $validated = $request->validated();

        $exists = Survey::query()
            ->whereDate('created_at', now()->toDateString())
            ->where('customer_id', $validated['customerId'])
            ->where('driver_id', $validated['driverId'])
            ->exists();

        if($exists) {
            return response()->json(['message' => "Survey untuk supir ini hanya bisa dilakukan 1 kali sehari"], 400);
        }

        try {
            DB::beginTransaction();
            $survey = Survey::query()->create([
                'customer_id' => $validated['customerId'],
                'driver_id' => $validated['driverId'],
                'channel_id' => $validated['channelId'],
            ]);

            $questions = $validated['questions'];
            $questions = array_map(
                fn($value, $key) => ['id' => str()->ulid(), 'question_id' => $key, 'value' => $value, 'survey_id' => $survey->id],
                $questions,
                array_keys($questions),
            );

            DB::table('survey_answers')->insert($questions);
            DB::commit();
        } catch(QueryException $e) {
            DB::rollBack();
            if ($e->getCode() == "23000") {
                $errorMessage = $e->getMessage();
                if (str_contains($errorMessage, 'question_id')) {
                    return response()->json(['message' => 'Terdapat pertanyaan yang tidak diketahui'], 400);
                }
            }

            return response()->json(['message' => "Terdapat kesalahan pada server, silakan coba lagi beberapa saat"], 500);
        } catch(Exception $e) {
            return response()->json(['message' => "Terdapat kesalahan pada server, silakan coba lagi beberapa saat"], 500);
        }

        return response()->json(['message' => 'OK'], 201);
    }

    public function searchCustomerID(Request $request)
    {
        $param = $request->query('search');
        if (empty($param)) {
            return [];
        }

        $customers = Customer::query()
            ->where('id_customer', 'LIKE', "{$param}%")
            ->limit(5)
            ->get(['id', 'id_customer', 'name']);

        return $customers;
    }

    public function searchDriverNIK(Request $request)
    {
        $param = $request->query('search');
        if (empty($param)) {
            return [];
        }

        $drivers = Driver::query()
            ->where('nik', 'LIKE', "{$param}%")
            ->limit(5)
            ->get(['id', 'nik', 'name']);

        return $drivers;
    }
}
