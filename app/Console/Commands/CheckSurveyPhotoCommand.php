<?php

namespace App\Console\Commands;

use App\Models\Survey;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Uri;

class CheckSurveyPhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-photo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check survey photo command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Survey::chunk(50, function (Collection $surveys) {
            $surveys->each(function (Survey $survey) {
                $imgUrl = $survey->img_url;

                $response = Http::get(
                    (string) Uri::of(Config::get('app.face_detection_url'))
                        ->withQuery(['img_url' => $imgUrl])
                );

                if (! $response->successful()) {
                    Log::warning('Check Survey Photo Command: Face detection url hit is not success',
                        ['id' => $this->surveyId, 'status' => $response->status()]
                    );

                    return;
                }

                $ok = $response->body() === 'False' ? false : true;

                if (! $ok) {
                    $survey->update([
                        'face_detected' => false,
                    ]);
                }

                Log::info("Done", ["img_url" => $survey->img_url]);
            });
        });
    }
}
