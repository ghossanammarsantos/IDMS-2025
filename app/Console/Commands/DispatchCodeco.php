<?php

namespace App\Console\Commands;

use App\Services\Edi\CodecoDispatchService;
use App\Support\EdiEvent;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DispatchCodeco extends Command
{
    protected $signature = 'edi:codeco:dispatch 
                            {--event=IN : IN atau OUT} 
                            {--start= : yyyy-mm-dd HH:ii:ss} 
                            {--end= : yyyy-mm-dd HH:ii:ss}
                            {--customer= : filter CUSTOMER_CODE}';

    protected $description = 'Generate & kirim CODECO per-customer hanya untuk entry yang belum terkirim.';

    public function handle(CodecoDispatchService $svc)
    {
        $ev = EdiEvent::fromOption($this->option('event'));

        $now   = now('Asia/Jakarta');
        $start = $this->option('start') ? Carbon::parse($this->option('start'), 'Asia/Jakarta') : $now->copy()->subHours(3)->seconds(0);
        $end   = $this->option('end')   ? Carbon::parse($this->option('end'),   'Asia/Jakarta') : $now->copy()->seconds(0);

        $customer = $this->option('customer') ? strtoupper($this->option('customer')) : null;

        $result = $svc->run($ev, $start, $end, $customer);

        $this->info("Window: {$result['window'][0]} → {$result['window'][1]}");
        $this->line(str_repeat('=', 42));
        $this->info('Total entries: ' . $result['total']);

        foreach (($result['by_customer'] ?? []) as $cust => $info) {
            $this->line(str_repeat('-', 42));
            $this->info("Customer : {$cust}");
            $this->line('Count    : ' . $info['count']);
            $this->line('File     : ' . $info['file']);
            $this->line('Uploaded : ' . ($info['uploaded'] ? 'YES' : 'NO'));
        }

        return 0;
    }
}
