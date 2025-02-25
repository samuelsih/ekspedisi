<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function survey_answers()
    {
        return $this->hasManyThrough(SurveyAnswer::class, Survey::class);
    }

    public function customer_survey_declines()
    {
        return $this->hasMany(CustomerSurveyDecline::class);
    }

    public function getContributionAttribute()
    {
        $totalSurvey = $this->surveys_count;
        $divider = $totalSurvey + $this->customer_survey_declines_count;
        $avgRating = $this->survey_answers_avg_value;

        if ($divider === 0) {
            return 0;
        }

        return round(($totalSurvey / $divider) * $avgRating, 3);
    }
}
