<?php

namespace App\Services\Edi;

use Carbon\Carbon;

/**
 * Builder EDI CODECO (EDIFACT)
 *
 * build($header, $containers, $event)
 * - $header:
 *   [
 *     'customer_code'   => 'HMM' | 'SIT' | ... (NULL => UNKNOWN; tetap dipakai untuk header NAD/MR/CF),
 *     'voyage'          => 'XXXX',
 *     'created_at'      => Carbon,
 *     'interchange_ref' => 'O2505091630',
 *     'message_ref'     => 'O2505091630001',
 *     'document_no'     => '2505091630403',
 *     'sender'          => null|override,
 *     'recipient'       => null|override,
 *     'carrier'         => null|override,
 *   ]
 *
 * - $containers: array of [
 *     'container_no','iso','booking_no','event_dt'(Carbon),
 *     'port_code'('IDBTM'), 'depot_code'('TBMIDBTM'),
 *     'gross_weight','payload','tare','maxgross',
 *     'status_container','grade_container',
 *     'voyage','consignee'
 *   ]
 *
 * - $event: "IN" | "OUT"
 */
class CodecoBuilder
{
    public function build(array $header, array $containers, string $event): string
    {
        $event = strtoupper($event);

        // -------- Profil & override khusus customer --------
        $cust = strtoupper((string)($header['customer_code'] ?? 'UNKNOWN'));

        // default (semua customer)
        $terminalCode = 'TBMIDBTM';
        $portCode     = 'IDBTM';
        $locDepotExt  = 'STO:DP3';      // untuk LOC segmen
        $mrCode       = $cust ?: 'UNKNOWN'; // NAD+MR / NAD+CF
        $carrierCode  = $cust ?: 'UNKNOWN';

        // TDT header default: "TDT+20++{voyage}++:172:87+++:146::'"
        $voyHdr = (string)($header['voyage'] ?? '');
        $tdtHeader = 'TDT+20++' . $voyHdr . '++:172:87+++:146::\'';

        // *** PROFIL SIT ***
        // - Kode terminal: TBMIDBAT
        // - MR/CF (customer code) => SITID
        // - TDT header: TDT+{voyage}++:172:87+++:146::'
        // - LOC:  IDBAT / TBMIDBAT:STO:DP3
        if ($cust === 'SIT') {
            $terminalCode = 'BAT';
            $portCode     = 'IDBAT';
            $locDepotExt  = 'STO:DP3';
            $mrCode       = 'SITID';
            $carrierCode  = 'SITID';
            $tdtHeader    = 'TDT+' . $voyHdr . '++:172:87+++:146::\'';
        }

        // override manual kalau ada
        if (!empty($header['sender']))   $terminalCode = strtoupper($header['sender']);
        if (!empty($header['recipient'])) $mrCode       = strtoupper($header['recipient']);
        if (!empty($header['carrier']))  $carrierCode  = strtoupper($header['carrier']);

        // -------- UNB / UNH / BGM / TDT(header) / NAD --------
        /** @var Carbon $now */
        $now    = $header['created_at'] ?? now('Asia/Jakarta');
        $icRef  = $header['interchange_ref'] ?? ('O' . $now->format('ymdHi'));
        $msgRef = $header['message_ref'] ?? ($icRef . '001');
        $docNo  = $header['document_no'] ?? $now->format('ymdHi') . '403';

        $lines   = [];
        $lines[] = 'UNB+UNOA:1+' . $terminalCode . '+' . $mrCode . '+' . $now->format('ymd:Hi') . '+' . $icRef . '\'';
        $lines[] = 'UNH+' . $msgRef . '+CODECO:D:95B:UN:ITG14\'';
        $lines[] = 'BGM+36+' . $docNo . '+9\'';
        $lines[] = $tdtHeader; // sesuai profil (SIT / selain SIT)
        $lines[] = 'NAD+MS+' . $terminalCode . ':160:87\'';
        $lines[] = 'NAD+MR+' . $mrCode . ':160:87\'';
        $lines[] = 'NAD+CF+' . $carrierCode . ':160:87\'';

        // -------- LOOP kontainer --------
        $cnt = 0;
        foreach ($containers as $c) {
            $cnt++;
            $cn   = strtoupper((string)($c['container_no'] ?? ''));
            $iso  = strtoupper((string)($c['iso'] ?? ''));
            $bkn  = (string)($c['booking_no'] ?? '');
            /** @var Carbon $evtDt */
            $evtDt = $c['event_dt'] instanceof Carbon ? $c['event_dt'] : Carbon::parse($c['event_dt'] ?? $now);

            $voyLine = (string)($c['voyage'] ?? $voyHdr); // fallback ke header voyage
            $cons    = (string)($c['consignee'] ?? '');
            $status  = strtoupper((string)($c['status_container'] ?? ''));
            $grade   = strtoupper((string)($c['grade_container'] ?? ''));

            // bobot prioritas: gross_weight -> payload -> maxgross -> 0
            $w = $c['gross_weight'] ?? $c['payload'] ?? $c['maxgross'] ?? 0;
            $w = (int) $w;

            // a) EQD
            $lines[] = 'EQD+CN+' . $cn . '+' . $iso . ':102:5+++4\'';

            // b) RFF (booking terisi)
            if ($bkn !== '') {
                $lines[] = 'RFF+BN:' . $bkn . '\'';
            } else {
                // tetap keluarkan baris RFF kosong (kalau tidak ada nomor booking)
                $lines[] = 'RFF+BN:\'';
            }

            // c) Tambah SELALU satu baris RFF kosong DI BAWAH RFF pertama (sesuai permintaan)
            $lines[] = 'RFF+BN:\'';

            // d) DTM (gate time)
            $lines[] = 'DTM+7:' . $evtDt->format('YmdHi') . ':203\'';

            // e) LOC
            //    LOC+165+{portCode}:139:6+{terminalCode}:{ext}'
            $lines[] = 'LOC+165+' . $portCode . ':139:6+' . $terminalCode . ':' . $locDepotExt . '\'';

            // f) MEA (selalu tampil; jika tak ada bobot → 0)
            $lines[] = 'MEA+AAE+G+KGM:' . $w . '\'';

            // g) FTX (selalu tampil; biarkan kosong bila value tidak ada)
            $lines[] = 'FTX+DAR+' . $status . '+' . $grade . '\'';

            // h) TDT (per-kontainer) — ZZZ → DP3
            //    TDT+1++3+++++{voyage}:146:DP3'
            $lines[] = 'TDT+1++3+++++' . $voyLine . ':146:DP3\'';

            // i) NAD+CZ (Consignee)
            $lines[] = 'NAD+CZ+++' . $cons . '\'';
        }

        // -------- Trailer --------
        $lines[] = 'CNT+16:' . $cnt . '\'';
        $lines[] = 'UNT+26+' . $msgRef . '\'';
        $lines[] = 'UNZ+1+' . $icRef . '\'';

        return implode("\n", $lines);
    }
}
