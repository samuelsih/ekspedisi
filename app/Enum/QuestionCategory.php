<?php

namespace App\Enum;

enum QuestionCategory: string
{
    case SUPIR = "Supir";
    case TOKO = "Toko";

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
