<?php

namespace App\Services\Edi;

use Carbon\Carbon;

class CodecoBuilder
{
    private $seg = "'";  // segment terminator
    private $elm = "+";  // data element separator
    private $cmp = ":";  // component data element separator

    private function esc($s)
    {
        $s = (string) $s;
        return str_replace([$this->seg, $this->elm, $this->cmp], ['?\'', '?+', '?:'], $s);
    }

    private function yymmddHm(Carbon $dt)
    {
        return [$dt->format('ymd'), $dt->format('Hi')];
    }

    private function sanAlnum($s)
    {
        $s = strtoupper((string) $s);
        $s = str_replace(["\xEF\xBB\xBF", "\xE2\x80\x8B", "\xC2\xA0"], '', $s); // BOM/ZWSP/NBSP
        $s = preg_replace('/[\x00-\x1F\x7F]/', '', $s);                          // control
        return preg_replace('/[^A-Z0-9]/', '', $s);                               // keep A-Z0-9
    }

    private function fmtYmdHm203(Carbon $dt)
    {
        // 203 = YYYYMMDDHHMM
        return $dt->format('YmdHi');
    }

    /**
     * Build CODECO lengkap dengan UNB/UNZ sesuai dokumen.
     * $header wajib: customer_code, voyage, created_at(Carbon), interchange_ref, message_ref, document_no
     * $containers: array of arrays (sudah dimap di Command)
     */
    public function build(array $header, array $containers): string
    {
        $S = $this->seg;
        $E = $this->elm;
        $C = $this->cmp;
        $out = [];

        $created = $header['created_at'] instanceof Carbon ? $header['created_at'] : now('Asia/Jakarta');
        [$yymmdd, $hhmm] = $this->yymmddHm($created);

        $icRef   = $this->esc($header['interchange_ref']); // OyyMMddHHmm
        $msgRef  = $this->esc($header['message_ref']);     // OyyMMddHHmm001
        $docNo   = $this->esc($header['document_no']);     // YYMMDDHHMM + 3 digit
        $voyHdr  = $this->sanAlnum($header['voyage'] ?? '');
        $cust    = $this->sanAlnum($header['customer_code'] ?? 'HMM'); // default HMM jika kosong

        // ===== UNB (interchange header)
        // UNB+UNOA:1+TBMIDBTM+{CUSTOMER_CODE}+YYMMDD:HHMM+{ICREF}'
        $out[] = "UNB{$E}UNOA:1{$E}TBMIDBTM{$E}{$cust}{$E}{$yymmdd}:{$hhmm}{$E}{$icRef}'";

        // ===== UNH (start of message)
        // UNH+{ICREF}001+CODECO:D:95B:UN:ITG14'
        $idxUnh = count($out); // simpan index untuk hitung UNT nanti
        $out[]  = "UNH{$E}{$msgRef}{$E}CODECO:D:95B:UN:ITG14'";

        // ===== BGM
        // BGM+36+{DOCNO}+9'
        $out[] = "BGM{$E}36{$E}{$docNo}{$E}9'";

        // ===== TDT+20
        // TDT+20++{VOYAGE}++:172:87+++:146::'
        $out[] = "TDT{$E}20{$E}{$E}{$voyHdr}{$E}{$E}:172:87{$E}{$E}{$E}:146::'";

        // ===== NAD (MS/MR/CF)
        $out[] = "NAD{$E}MS{$E}TBMIDBTM:160:87'";
        $out[] = "NAD{$E}MR{$E}{$cust}:160:87'";
        $out[] = "NAD{$E}CF{$E}{$cust}:160:87'";

        // ===== Loop per container
        foreach ($containers as $c) {
            $cn       = $this->sanAlnum($c['container_no'] ?? '');
            $iso      = $this->esc(strtoupper($c['iso'] ?? ''));
            $voyPer   = $this->sanAlnum($c['voyage'] ?? $voyHdr);
            $booking  = (string) ($c['booking_no'] ?? '');
            $gateDt   = $c['event_dt'] instanceof Carbon ? $c['event_dt'] : Carbon::parse($c['event_dt'] ?? $created);
            $gateYmdH = $this->fmtYmdHm203($gateDt);
            $gross    = $c['gross_weight'] ?? ($c['payload'] ?? null);
            $statusC  = $this->sanAlnum($c['status_container'] ?? '');
            $gradeC   = $this->sanAlnum($c['grade_container']  ?? '');
            $cons     = $this->esc($c['consignee'] ?? '');
            $shipId   = $this->sanAlnum($c['ship_id'] ?? ''); // opsional; jika kosong tetap valid

            // 1) EQD+CN+{NO_CONTAINER}+{SIZE_TYPE}:102:5+++4'
            $out[] = "EQD{$E}CN{$E}{$cn}{$E}{$iso}{$C}102{$C}5{$E}{$E}{$E}4'";

            // 2) RFF+BN:{BOOKING}' atau RFF+BN:' jika kosong
            if ($booking !== '') {
                $out[] = "RFF{$E}BN:{$this->esc($booking)}'";
            } else {
                $out[] = "RFF{$E}BN:'";
            }

            // 3) DTM+7:{YYYYMMDDHHMM}:203'   ← FIX: pakai ':' setelah '7'
            $out[] = "DTM{$E}7{$C}{$gateYmdH}{$C}203'";

            // 4) LOC+165+IDBTM:139:6+TBMIDBTM:STO:ZZZ'
            $out[] = "LOC{$E}165{$E}IDBTM:139:6{$E}TBMIDBTM:STO:ZZZ'";

            // 5) MEA+AAE+G+KGM:{GROSS}'
            if (!empty($gross)) {
                $grossVal = preg_replace('/[^0-9]/', '', (string) $gross);
                if ($grossVal !== '') {
                    $out[] = "MEA{$E}AAE{$E}G{$E}KGM:{$grossVal}'";
                }
            }

            // 6) FTX+DAR+{STATUS_CONTAINER}+{GRADE_CONTAINER}'
            if ($statusC !== '' || $gradeC !== '') {
                $out[] = "FTX{$E}DAR{$E}{$statusC}{$E}{$gradeC}'";
            }

            // 7) TDT+1++{VOYAGE}+++++{SHIP_ID}:146:DP3'  ← FIX: DP3 jadi QUALIFIER (komponen ke-3)
            $out[] = "TDT{$E}1{$E}{$E}{$voyPer}{$E}{$E}{$E}{$E}{$E}{$shipId}:146:DP3'";

            // 8) NAD+CZ+++{CONSIGNEE}'
            $out[] = "NAD{$E}CZ{$E}{$E}{$E}{$cons}'";
        }

        // CNT+16:{TOTAL}'  ← FIX: pakai ':'
        $cnt = count($containers);
        $out[] = "CNT{$E}16{$C}{$cnt}'";

        // UNT: jumlah segmen dari UNH..UNT (termasuk UNH dan UNT)
        // saat ini $idxUnh adalah index elemen UNH (0-based)
        // total segmen dari UNH sampai baris terakhir out: (count($out) - $idxUnh)
        // tambah 1 untuk UNT itu sendiri
        $segmentsBetween = count($out) - $idxUnh; // UNH..(CNT)
        $untCount = $segmentsBetween + 1;         // + UNT
        $out[] = "UNT{$E}{$untCount}{$E}{$msgRef}'";

        // UNZ+1+{ICREF}'
        $out[] = "UNZ{$E}1{$E}{$icRef}'";

        return implode("\n", $out) . "\n";
    }
}
