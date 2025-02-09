<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasUlids;

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function survey_answers()
    {
        return $this->hasManyThrough(SurveyAnswer::class, Survey::class);
    }
}
