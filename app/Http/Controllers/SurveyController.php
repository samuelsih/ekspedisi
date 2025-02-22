<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyRequest;
use App\Http\Requests\SurveyWithoutChannelRequest;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Question;
use App\Models\WebConfig;
use App\Service\SurveyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SurveyController extends Controller
{
    public function __construct(
        private readonly SurveyService $service,
    ) {}

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
            'channels' => $channels,
        ]);
    }

    public function indexWithoutChannel()
    {
        $questions = Question::query()->where('is_active', true)->get(['id', 'title']);

        $title = WebConfig::query()
            ->where('name', 'Judul halaman survey')
            ->first('value')['value'];

        $subtitle = WebConfig::query()
            ->where('name', 'Sub judul halaman survey')
            ->first('value')['value'];

        return Inertia::render('SurveyWithoutChannel', [
            'title' => $title,
            'subtitle' => $subtitle,
            'questions' => $questions,
        ]);
    }

    public function storeWithoutChannel(SurveyWithoutChannelRequest $request)
    {
        $validated = $request->validated();

        $channel = Channel::query()->where('name', 'GT')->first();
        if (empty($channel)) {
            return response()->json(['message' => 'Gagal mengambil data. Coba beberapa saat lagi'], 500);
        }

        $validated['channelId'] = $channel['id'];

        if ($this->service->surveyExistsToday($validated['customerId'])) {
            return response()->json(['message' => 'Survey untuk toko ini hanya bisa dilakukan 1 kali sehari'], 400);
        }

        try {
            $file = $request->file('image');
            $fileName = str()->random(40).'.'.$file->getClientOriginalExtension();
            $path = Storage::disk('s3')->putFileAs('validation', $file, $fileName, 'public');
            $imageURL = Storage::disk('s3')->url($path);
        } catch (Exception) {
            return response()->json(['message' => 'Gagal mengunggah gambar. Coba beberapa saat lagi'], 500);
        }

        return $this->service->saveSurveyData($validated, $imageURL);
    }

    public function store(SurveyRequest $request)
    {
        $validated = $request->validated();

        if ($this->service->surveyExistsToday($validated['customerId'])) {
            return response()->json(['message' => 'Survey untuk toko ini hanya bisa dilakukan 1 kali sehari'], 400);
        }

        try {
            $file = $request->file('image');
            $fileName = str()->random(40).'.'.$file->getClientOriginalExtension();
            $path = Storage::disk('s3')->putFileAs('validation', $file, $fileName, 'public');
            $imageURL = Storage::disk('s3')->url($path);
        } catch (Exception) {
            return response()->json(['message' => 'Gagal mengunggah gambar. Coba beberapa saat lagi'], 500);
        }

        return $this->service->saveSurveyData($validated, $imageURL);
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
