<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SurveyInTodayExport;
use App\Mail\SurveyInTodayMail;

class SendSurveyInReport extends Command
{
    protected $signature = 'report:surveyin:send';
    protected $description = 'Kirim email report Survey IN hari ini (setiap 3 jam)';

    public function handle(): int
    {
        $tz      = 'Asia/Jakarta';
        $now     = Carbon::now($tz);
        $dateStr = $now->format('Y-m-d_His');

        // Generate XLSX di memory (tanpa file fisik)
        $binary = Excel::raw(new SurveyInTodayExport(), \Maatwebsite\Excel\Excel::XLSX);
        $filename = "survey_in_{$now->format('Y-m-d')}_{$dateStr}.xlsx";

        // Penerima tetap (bisa Anda buat config jika perlu)
        $to = [
            'ghossan@perserobatam.id',
            'ali@perserobatam.com',
            'hadhrat.khalil@perserobatam.com',
            'cs.depo@perserobatam.com',
        ];

        // Kirim
        Mail::to($to)->send(new SurveyInTodayMail($binary, $filename, $tz));

        $this->info("Report Survey IN terkirim: {$filename}");
        return self::SUCCESS;
    }
}
