<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SurveyInTodayMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $sentAtString;
    protected string $filename;
    protected string $xlsxBinary;

    /**
     * @param string $xlsxBinary   // isi file XLSX (binary)
     * @param string $filename     // nama file lampiran
     */
    public function __construct(string $xlsxBinary, string $filename, string $tz = 'Asia/Jakarta')
    {
        $this->xlsxBinary = $xlsxBinary;
        $this->filename   = $filename;
        $this->sentAtString = Carbon::now($tz)->format('d-m-Y H:i:s');
    }

    public function build()
    {
        return $this->from('it@perserobatam.id', 'IDMS Bot')
            ->subject('Report Survey IN - ' . $this->sentAtString)
            ->view('emails.surveyin_today') // lihat file blade di langkah #3
            ->attachData(
                $this->xlsxBinary,
                $this->filename,
                ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
    }
}
