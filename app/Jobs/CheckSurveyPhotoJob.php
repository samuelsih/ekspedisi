<?php

namespace App\Jobs;

use App\Models\Feature;
use App\Models\Survey;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Uri;

class CheckSurveyPhotoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $surveyId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! Feature::active('survey-face-detection')) {
            return;
        }

        $image = Survey::query()->where('id', $this->surveyId)->first(['img_url']);
        if (empty($image)) {
            Log::warning('Survey is not found for image detection', ['id' => $this->surveyId]);

            return;
        }

        $imgUrl = $image['img_url'];

        $response = Http::get(
            (string) Uri::of(Config::get('app.face_detection_url'))
                ->withQuery(['img_url' => $imgUrl])
        );

        if (! $response->successful()) {
            Log::warning('Face detection url hit is not success',
                ['id' => $this->surveyId, 'status' => $response->status()]
            );

            return;
        }

        $ok = $response->body() === 'False' ? false : true;

        if (! $ok) {
            Survey::query()->where('id', $this->surveyId)->update([
                'face_detected' => false,
            ]);
        }
    }
}
