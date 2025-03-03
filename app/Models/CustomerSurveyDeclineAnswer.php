<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSurveyDeclineAnswer extends Model
{
    use HasUlids, SoftDeletes;

    public function customer_survey_declines()
    {
        return $this->hasMany(CustomerSurveyDecline::class);
    }
}
