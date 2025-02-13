<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasUlids, SoftDeletes;

    public function getQuestionValueAttribute()
    {
        return $this->survey_answers()->value;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function survey_answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }
}
