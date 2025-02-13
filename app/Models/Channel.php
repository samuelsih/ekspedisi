<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasUlids, SoftDeletes;

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }
}
