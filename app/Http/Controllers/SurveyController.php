<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Question;
use App\Models\WebConfig;
use Illuminate\Http\Request;
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
