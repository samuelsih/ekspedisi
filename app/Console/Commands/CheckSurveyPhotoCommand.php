<?php

namespace App\Console\Commands;

use App\Models\Survey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CheckSurveyPhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'survey:validate-img';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all surveys photo using face detector';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dockerImgName = Config::get('app.face_detection_image');

        foreach(Survey::query()->withoutTrashed()->latest()->lazy() as $survey) {
            $ok = shell_exec('docker run --rm '.$dockerImgName.' "'.$survey->img_url.'"') === 'False' ? false : true;
            if(! $ok) {
                Survey::query()->where('id', $survey->id)->update([
                    'face_detected' => false,
                ]);

                echo 'Suspicious ' . $survey->id . ' ' . $survey->img_url . PHP_EOL;
            }
        }
    }
}
