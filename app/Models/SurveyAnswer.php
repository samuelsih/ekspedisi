<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyAnswer extends Model
{
    use HasUlids, SoftDeletes;

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
