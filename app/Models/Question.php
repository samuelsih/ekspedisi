<?php

namespace App\Models;

use App\Enum\QuestionCategory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasUlids;

    public function survey_answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    public function getNameAttribute()
    {
        return $this->title;
    }
}
