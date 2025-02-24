<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    public static function active(string $feature)
    {
        $res = self::query()->where('name', $feature)->first();
        if (! $res) {
            return false;
        }

        $active = $res['is_active'];
        if ($active) {
            return true;
        }

        return false;
    }
}
