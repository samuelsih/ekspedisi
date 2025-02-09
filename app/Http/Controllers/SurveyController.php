<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Question;
use App\Models\WebConfig;
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

}
