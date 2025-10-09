<?php

namespace App\Services\Edi;

use Carbon\Carbon;

class CodecoBuilder
{
    private string $seg = "'";  // segment terminator
    private string $elm = "+";  // data element separator
    private string $cmp = ":";  // component data element separator

    private function esc(?string $v): string
    {
        $v = (string) $v;
        $v = str_replace(["\r", "\n"], ' ', $v);
        return str_replace($this->seg, ' ', trim($v));
    }

    private function yymmddHm(Carbon $dt): array
    {
        return [$dt->format('ymd'), $dt->format('Hi')]; // ex: 250509, 1630
    }

    private function yyyymmddHm(Carbon $dt): string
    {
        return $dt->format('YmdHi'); // ex: 202505091527
    }

    /**
     * Bangun 1 interchange berisi 1 message CODECO (format contoh di Word/TXT kamu).
     * $header keys (wajib):
     * - sender, recipient, carrier, voyage, created_at(Carbon), interchange_ref, message_ref, document_no
     * $containers: array item:
     * - container_no, iso, status('4' full, '1' empty), booking_no|null, event_dt(Carbon),
     *   port_code, depot_code, gross_weight|null, damage_code('1' default), feeder_voy, vessel_call, consignee|null
     */
    public function build(array $header, array $containers): string
    {
        $created = $header['created_at'];
        [$yymmdd, $hhmm] = $this->yymmddHm($created);

        $S = $this->seg;
        $E = $this->elm;
        $C = $this->cmp;

        // === UNB (sesuai contoh: ada control reference di belakang)
        $edi  = "UNB{$E}UNOA:1{$E}{$this->esc($header['sender'])}{$E}{$this->esc($header['recipient'])}{$E}{$yymmdd}:{$hhmm}{$E}{$this->esc($header['interchange_ref'])}{$S}";

        // === UNH
        $msg = [];
        $msg[] = "UNH{$E}{$this->esc($header['message_ref'])}{$E}CODECO:D:95B:UN:ITG14{$S}";

        // BGM
        $msg[] = "BGM{$E}36{$E}{$this->esc($header['document_no'])}{$E}9{$S}";

        // TDT main (sea)
        $voyage = $this->esc($header['voyage'] ?? '1');
        $msg[] = "TDT{$E}20{$E}{$E}{$voyage}{$E}{$E}:172:87{$E}{$E}{$E}:146::{$S}";

        // NAD sender/recipient/carrier
        $msg[] = "NAD{$E}MS{$E}{$this->esc($header['sender'])}:160:87{$S}";
        $msg[] = "NAD{$E}MR{$E}{$this->esc($header['recipient'])}:160:87{$S}";
        $msg[] = "NAD{$E}CF{$E}{$this->esc($header['carrier'])}:160:87{$S}";

        // === Loop container
        foreach ($containers as $c) {
            $cn  = strtoupper($this->esc($c['container_no']));
            $iso = strtoupper($this->esc($c['iso']));
            $st  = $this->esc($c['status'] ?? '4');

            $msg[] = "EQD{$E}CN{$E}{$cn}{$E}{$iso}:102:5{$E}{$E}{$E}{$st}{$S}";

            $booking = trim((string)($c['booking_no'] ?? ''));
            if ($booking !== '') {
                $msg[] = "RFF{$E}BN:{$this->esc($booking)}{$S}";
            }
            // kirim baris kosong juga (sesuai contoh kamu)
            $msg[] = "RFF{$E}BN:{$S}";

            $event = $c['event_dt'];
            $msg[] = "DTM{$E}7:{$this->yyyymmddHm($event)}:203{$S}";

            $port  = strtoupper($this->esc($c['port_code']));
            $depot = strtoupper($this->esc($c['depot_code']));
            $msg[] = "LOC{$E}165{$E}{$port}:139:6{$E}{$depot}:STO:ZZZ{$S}";

            if (!empty($c['gross_weight'])) {
                $gw = (int)$c['gross_weight'];
                $msg[] = "MEA{$E}AAE{$E}G{$E}KGM:{$gw}{$S}";
            }

            $msg[] = "FTX{$E}DAR{$E}{$E}{$this->esc($c['damage_code'] ?? '1')}{$S}";

            $fv = $this->esc($c['feeder_voy'] ?? '3');
            $vc = $this->esc($c['vessel_call'] ?? '');
            $msg[] = "TDT{$E}1{$E}{$E}{$fv}{$E}{$E}{$E}{$E}{$E}{$vc}:146:ZZZ{$S}";

            if (!empty($c['consignee'])) {
                $msg[] = "NAD{$E}CZ{$E}{$E}{$E}{$this->esc($c['consignee'])}{$S}";
            }
        }

        // CNT: jumlah container
        $msg[] = "CNT{$E}16:" . count($containers) . $S;

        // UNT: jumlah segmen dari UNH..UNT (inklusif)
        $segmentCount = count($msg) + 1;
        $msg[] = "UNT{$E}{$segmentCount}{$E}{$this->esc($header['message_ref'])}{$S}";

        // gabung message + UNZ
        $edi .= implode('', $msg);
        $edi .= "UNZ{$E}1{$E}{$this->esc($header['interchange_ref'])}{$S}";

        return $edi;
    }
}
