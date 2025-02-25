<?php

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Feature;
use App\Models\Question;
use App\Models\WebConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

beforeEach(function () {
    Customer::factory(100)->create();
    Driver::factory(100)->create();
});

test('should render survey page', function () {
    $title = WebConfig::query()
        ->where('name', 'Judul halaman survey')
        ->first('value')['value'];

    $subtitle = WebConfig::query()
        ->where('name', 'Sub judul halaman survey')
        ->first('value')['value'];

    $questions = Question::query()->where('is_active', true)->get(['id', 'title']);

    $this->get('/')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('SurveyWithoutChannel')
            ->where('title', $title)
            ->where('subtitle', $subtitle)
            ->where('questions', $questions)
            ->missing('channels')
        );
});

test('should render anti survey page', function () {
    $this->assertTrue(
        Feature::active('customer-survey-decline')
    );

    $title = WebConfig::query()
        ->where('name', 'Judul halaman penolakan survey')
        ->first('value')['value'];

    $subtitle = WebConfig::query()
        ->where('name', 'Sub judul halaman penolakan survey')
        ->first('value')['value'];

    $this->get('/decline-survey')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('AntiSurvey')
            ->where('title', $title)
            ->where('subtitle', $subtitle)
            ->missing('questions')
            ->missing('channels')
        );
});
