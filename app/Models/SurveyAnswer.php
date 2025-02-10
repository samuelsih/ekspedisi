<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    use HasUlids;

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
