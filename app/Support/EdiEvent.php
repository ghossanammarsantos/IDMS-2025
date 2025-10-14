<?php

namespace App\Support;

final class EdiEvent
{
    public const IN  = 'IN';
    public const OUT = 'OUT';

    public static function fromOption($opt)
    {
        $opt = strtoupper((string) $opt);
        return $opt === self::OUT ? self::OUT : self::IN;
    }
}
