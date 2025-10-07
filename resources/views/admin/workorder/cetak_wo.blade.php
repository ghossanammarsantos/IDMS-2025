<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order Depo Container</title>
    <style>
        body {
            font-family: times, "Times New Roman";
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }

        table th {
            background-color: #f2f2f2;
        }

        table tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
        }

        table thead th:first-child,
        table thead th:last-child {
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        table tfoot td {
            text-align: right;
            font-weight: bold;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 20px;
        }

        .container-fluid {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .borderless {
            border: none !important;
        }

        .text-end {
            text-align: end;
        }

        .text-start {
            text-align: start;
        }
    </style>
</head>

<body>

    <div class="row">
        <div class="col-12">
            <table>
                <tr>
                    <td width="150px">
                        <img src="{{ asset('app-assets/images/logo/logopb.png') }}" width="140px" height="50px" alt="PT. Persero Batam Logo">
                    </td>
                    <td width="700px">
                        <b style="font-size:25px;">PT. Pengusahaan Daerah Industri Pulau Batam</b><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="border:1px solid #0B4269; padding:10px; border-radius:10px; background-color:#CEE6F3; font-size:10px; text-align: center;">
                            Kantor Pusat : Jl. Yos Sudarso No.01, Kec. Batu Ampar, Kota Batam, Kepulauan Riau
                            <br>
                            Kantor Perwakilan : Sentra Pemuda, Jl. Pemuda Raya No. 61 Kav. 3A, Rawamangun - Jakarta Timur
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <div style="padding-top:10px; border-radius:10px; color:#1D5D9B; font-size:30px; ">
                            <u>Work Order Depo Container</u>
                        </div>
                        <div style="border-radius:10px; color:#1D5D9B; font-size:15px; ">
                            Nomor Kegiatan : {{ $work_order->nomor_wo }}
                        </div>
                    </td>
                </tr>
            </table>

            <table style="margin-top:0px;">
                <tr>
                    <td colspan="3">
                        <div style="color:black; font-size:18px; ">
                            Customer : {{ $work_order->nama_customer }}
                        </div>
                        <div style="color:black; font-size:18px; ">
                            Tanggal / Waktu : {{ $work_order->set_time }}
                        </div>
                    </td>
                    <td colspan="3">
                        <div style="color:black; font-size:12px; ">
                            Nama Kapal : {{ $work_order->nama_kapal }}
                        </div>
                        <div style="color:black; font-size:12px; ">
                            Voyage : {{ $work_order->voyage }}
                        </div>
                        <div style="color:black; font-size:12px; ">
                            Shipper : {{ $work_order->shipper }}
                        </div>
                    </td>
                </tr>
            </table>

            <br><br>
            <!-- Tabel detail biaya -->
            <table class="table table-borderless" style="border:1px solid #0B4269; padding:10px; border-radius:10px; font-size:11px;">
                <thead>
                    <tr>
                        <th>No. Container</th>
                        <th>Ukuran Container</th>
                        <th>Status</th>
                        <th>Kegiatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data_detail as $detail)
                    <tr>
                        <td>{{ $detail->no_container }}</td>
                        <td>{{ $detail->size_type }}</td>
                        <td>{{ $detail->status_container }}</td>
                        <td>{{ $detail->kegiatan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br><br>
            <!-- Tabel informasi tambahan -->
            <table>
                <tr>
                    <td width="500px" valign="top">
                        <!-- Informasi tambahan seperti Terbilang Total Nominal, Catatan, dll -->
                        <div style="font-size: 12px;">
                            <b>Informasi Tambahan:</b><br>
                            <br>
                            Catatan: 
                        </div>
                    </td>
                    <td valign="top">
                        <!-- TTD dan informasi penanggung jawab -->
                        <table>
                            <tr>
                                <td style="width: 120px; text-align: center; padding-bottom: 10px;">
                                    <div style="border-bottom: 1px solid #000; width: 100%; height: 60px;">Surveyor</div>
                                </td>
                                <td style="width: 120px; text-align: center; padding-bottom: 10px;">
                                    <div style="border-bottom: 1px solid #000; width: 100%; height: 60px;">Admin</div>
                                </td>
                                <td style="width: 120px; text-align: center; padding-bottom: 10px;">
                                    <div style="border-bottom: 1px solid #000; width: 100%; height: 60px;">GM Operasi</div>
                                </td>
                                <td style="width: 120px; text-align: center; padding-bottom: 10px;">
                                    <div style="border-bottom: 1px solid #000; width: 100%; height: 60px;">Customer</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>

</html>
