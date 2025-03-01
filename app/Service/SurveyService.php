<?php

namespace App\Service;

use App\Jobs\CheckSurveyPhotoJob;
use App\Models\Customer;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class SurveyService
{
    public function surveyExistsToday(string $customerId): bool
    {
        return Survey::query()
            ->whereDate('created_at', now()->toDateString())
            ->where('customer_id', $customerId)
            ->exists();
    }

    public function saveSurveyData(array $validated, string $imageURL)
    {
        try {
            DB::beginTransaction();

            $customerSurvey = Customer::query()
                ->where('id', $validated['customerId'])
                ->orWhere('id_customer', $validated['customerId'])
                ->first();

            if (! $customerSurvey) {
                $customerSurvey = Customer::create(['id_customer' => $validated['customerId']]);
            }

            $survey = Survey::create([
                'customer_id' => $customerSurvey->id,
                'driver_id' => $validated['driverId'],
                'channel_id' => $validated['channelId'],
                'img_url' => $imageURL,
            ]);

            $this->insertSurveyAnswers($survey, $validated['questions']);

            DB::commit();

            CheckSurveyPhotoJob::dispatch($survey->id);

            return response()->json(['message' => 'OK'], 201);
        } catch (QueryException $e) {
            DB::rollBack();

            return $this->handleQueryException($e);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Gagal menyimpan data. Coba beberapa saat lagi'], 500);
        }
    }

    private function insertSurveyAnswers(Survey $survey, array $questions)
    {
        $answers = collect($questions)->map(function ($value, $key) {
            return new SurveyAnswer([
                'question_id' => $key,
                'value' => $value,
            ]);
        });

        $survey->survey_answers()->saveMany($answers);
    }

    private function handleQueryException(QueryException $e)
    {
        if ($e->getCode() == '23000') {
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'question_id')) {
                return response()->json(['message' => 'Terdapat pertanyaan yang tidak diketahui'], 400);
            }
        }

        return response()->json(['message' => 'Gagal menyimpan data. Coba beberapa saat lagi'], 500);
    }
}
