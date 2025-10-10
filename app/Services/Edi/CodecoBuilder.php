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
     * Bangun 1 interchange berisi 1 message CODECO.
     * $header:
     * - sender, recipient, carrier, voyage, created_at(Carbon), interchange_ref, message_ref, document_no
     * $containers[]:
     * - container_no, iso, status('4'/'1'), booking_no, event_dt(Carbon),
     *   port_code, depot_code, gross_weight|null,
     *   status_container, grade_container, voyage, consignee|null
     */
    public function build(array $header, array $containers): string
    {
        $created = $header['created_at'];
        [$yymmdd, $hhmm] = $this->yymmddHm($created);

        // **satu segmen satu baris**
        $S = $this->seg . PHP_EOL;
        $E = $this->elm;
        $C = $this->cmp;

        // === UNB
        $edi  = "UNB{$E}UNOA:1{$E}{$this->esc($header['sender'])}{$E}{$this->esc($header['recipient'])}{$E}{$yymmdd}:{$hhmm}{$E}{$this->esc($header['interchange_ref'])}{$S}";

        // === UNH
        $msg = [];
        $msg[] = "UNH{$E}{$this->esc($header['message_ref'])}{$E}CODECO:D:95B:UN:ITG14{$S}";

        // BGM
        $msg[] = "BGM{$E}36{$E}{$this->esc($header['document_no'])}{$E}9{$S}";

        // TDT header (pakai voyage dari ANN_IMPORT jika ada)
        $voyageHdr = $this->esc($header['voyage'] ?? '');
        // format: TDT+20++(VOYAGE)++:172:87+++:146::'
        $msg[] = "TDT{$E}20{$E}{$E}{$voyageHdr}{$E}{$E}:172:87{$E}{$E}{$E}:146::{$S}";

        // NAD sender/recipient/carrier
        $msg[] = "NAD{$E}MS{$E}{$this->esc($header['sender'])}:160:87{$S}";
        $msg[] = "NAD{$E}MR{$E}{$this->esc($header['recipient'])}:160:87{$S}";
        $msg[] = "NAD{$E}CF{$E}{$this->esc($header['carrier'])}:160:87{$S}";

        // === Loop container
        foreach ($containers as $c) {
            $cn  = strtoupper($this->esc($c['container_no']));
            $iso = strtoupper($this->esc($c['iso']));
            $st  = $this->esc($c['status'] ?? '4');

            // EQD
            $msg[] = "EQD{$E}CN{$E}{$cn}{$E}{$iso}:102:5{$E}{$E}{$E}{$st}{$S}";

            // RFF booking
            $booking = trim((string)($c['booking_no'] ?? ''));
            if ($booking !== '') {
                $msg[] = "RFF{$E}BN:{$this->esc($booking)}{$S}";
            }
            // baris RFF kosong (sesuai contoh)
            $msg[] = "RFF{$E}BN:{$S}";

            // DTM 7
            $event = $c['event_dt'];
            $msg[] = "DTM{$E}7:{$this->yyyymmddHm($event)}:203{$S}";

            // LOC (port & depot)
            $port  = strtoupper($this->esc($c['port_code']));
            $depot = strtoupper($this->esc($c['depot_code']));
            $msg[] = "LOC{$E}165{$E}{$port}:139:6{$E}{$depot}:STO:ZZZ{$S}";

            // MEA (gross weight) kalau ada
            if (!empty($c['gross_weight'])) {
                $gw = (int)$c['gross_weight'];
                $msg[] = "MEA{$E}AAE{$E}G{$E}KGM:{$gw}{$S}";
            }

            // FTX DAR: gunakan STATUS_CONTAINER & GRADE_CONTAINER (bukan damage_code)
            $sc = $this->esc($c['status_container'] ?? '');
            $gc = $this->esc($c['grade_container'] ?? '');
            $msg[] = "FTX{$E}DAR{$E}{$sc}{$E}{$gc}{$S}";

            // TDT per-kontainer (pakai VOYAGE dari ANN_IMPORT; DP3 di qualifier)
            $voy = $this->esc($c['voyage'] ?? '');
            // format: TDT+1++(VOYAGE)+++++:146:(DP3)'
            $msg[] = "TDT{$E}1{$E}{$E}{$voy}{$E}{$E}{$E}{$E}{$E}:146:DP3{$S}";

            // Consignee (jika ada)
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
        $edi = $edi . implode('', $msg);
        $edi .= "UNZ{$E}1{$E}{$this->esc($header['interchange_ref'])}{$S}";

        return $edi;
    }
}
