<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasUlids, SoftDeletes;

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
}
