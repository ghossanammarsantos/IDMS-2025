<?php

namespace App\Support;

final class EdiType
{
    public const CODECO = 'CODECO';
    public const COARRI = 'COARRI'; // future-proof

    public static function ensure(string $s): string
    {
        $u = strtoupper($s);
        if (!in_array($u, [self::CODECO, self::COARRI], true)) {
            throw new \InvalidArgumentException("Unknown EDI type: {$s}");
        }
        return $u;
    }
}
