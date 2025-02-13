<?php

namespace App\Models;

use App\Enum\QuestionCategory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasUlids, SoftDeletes;

    public function survey_answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    public function getNameAttribute()
    {
        return $this->title;
    }
}
