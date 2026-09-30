<?php

namespace App\Libraries;

/**
 * ICParser - Malaysia IC Number Parser
 * Format: YYMMDD-PB-XXXX
 */
class ICParser
{
    public static function parse(string $ic): array|false
    {
        $digits = preg_replace('/\D/', '', $ic);
        if (strlen($digits) !== 12) return false;

        $yy   = substr($digits, 0, 2);
        $mm   = substr($digits, 2, 2);
        $dd   = substr($digits, 4, 2);
        $last = (int) substr($digits, -1);

        $year = ((int)$yy <= (int)date('y')) ? '20' . $yy : '19' . $yy;

        if ((int)$mm < 1 || (int)$mm > 12 || (int)$dd < 1 || (int)$dd > 31) return false;

        return [
            'dob'    => $year . '-' . $mm . '-' . $dd,
            'gender' => ($last % 2 !== 0) ? 'Lelaki' : 'Perempuan',
        ];
    }

    public static function format(string $ic): string
    {
        $digits = preg_replace('/\D/', '', $ic);
        if (strlen($digits) !== 12) return $ic;
        return substr($digits, 0, 6) . '-' . substr($digits, 6, 2) . '-' . substr($digits, 8, 4);
    }

    public static function validate(string $ic): bool
    {
        $digits = preg_replace('/\D/', '', $ic);
        return strlen($digits) === 12;
    }
}
