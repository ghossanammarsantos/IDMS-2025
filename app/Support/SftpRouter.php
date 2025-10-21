<?php

namespace App\Support;

class SftpRouter
{
    public static function forCustomer(string $customer): ?string
    {
        $map = [
            'HMM' => 'sftp_hmm',
            'SIT' => 'sftp_sit',
            // Tambah mapping lain kalau sudah siap konfig disknya
            // 'SIT' => 'sftp_sit',
            // 'MER' => 'sftp_mer',
        ];

        return $map[strtoupper(trim($customer))] ?? null;
    }
}
