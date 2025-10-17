<?php

namespace App\Support;

/**
 * Helper kecil untuk normalisasi event EDI.
 * Dipakai oleh command & service (Laravel 7 / PHP 7.4).
 */
final class EdiEvent
{
    public const IN  = 'IN';
    public const OUT = 'OUT';

    /**
     * Normalisasi dari opsi CLI (null / string bebas) ke 'IN' atau 'OUT'.
     * Default: IN.
     *
     * @param  string|null  $opt
     * @return string  'IN'|'OUT'
     */
    public static function fromOption($opt)
    {
        $v = strtoupper(trim((string) $opt));
        return $v === self::OUT ? self::OUT : self::IN;
    }

    /**
     * Pastikan string event valid ('IN'|'OUT') atau lempar exception.
     *
     * @param  string  $value
     * @return string  'IN'|'OUT'
     */
    public static function ensure($value)
    {
        $u = strtoupper((string) $value);
        if ($u !== self::IN && $u !== self::OUT) {
            throw new \InvalidArgumentException("Unknown EDI event: {$value}");
        }
        return $u;
    }

    /**
     * Apakah event adalah IN?
     *
     * @param  string  $value
     * @return bool
     */
    public static function isIn($value)
    {
        return strtoupper((string) $value) === self::IN;
    }

    /**
     * Apakah event adalah OUT?
     *
     * @param  string  $value
     * @return bool
     */
    public static function isOut($value)
    {
        return strtoupper((string) $value) === self::OUT;
    }

    /**
     * Konversi ke label event type yang dipakai di tabel: 'GATEIN' atau 'GATEOUT'.
     *
     * @param  string  $value  'IN'|'OUT'
     * @return string          'GATEIN'|'GATEOUT'
     */
    public static function toEventType($value)
    {
        return self::isOut($value) ? 'GATEOUT' : 'GATEIN';
    }
}
