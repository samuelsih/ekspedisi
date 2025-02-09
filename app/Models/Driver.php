<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasUlids;

    public function survey_answers()
    {
        return $this->hasManyThrough(SurveyAnswer::class, Survey::class);
    }
}
