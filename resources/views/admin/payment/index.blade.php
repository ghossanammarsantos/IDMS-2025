@extends('admin.layouts.app', 
    ['activePage' => 'Master'
])

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Payment Success</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="table-responsive">
                                            <table id="table-pay" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Nomor Payment</th>
                                                        <th>Nomor WO</th>
                                                        <th>Nama Customer</th>
                                                        <th>Total Bayar</th>
                                                        <th>Metode Pembayaran</th>
                                                        <th>Waktu Pembayaran</th>
                                                        <th>Cash Receipt</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($paymentList as $payment)
                                                        <tr>
                                                            <td>{{ $payment->nomor_payment }}</td>
                                                            <td>{{ $payment->nomor_wo }}</td>
                                                            <td>{{ $payment->nama_customer }}</td>
                                                            <td>{{ $payment->total_payment }}</td>
                                                            <td>{{ $payment->payment_method }}</td>
                                                            <td>{{ $payment->created_at }}</td>
                                                            <td>
                                                                <button class="btn btn-black btn-sm">
                                                                        <a class="fa fa-print" href="{{ route('payment.cetak_inv', ['nomor_wo' => $payment->nomor_wo]) }}">
                                                                            Cetak INV
                                                                        </a>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
        <!-- Tambahkan jQuery sebelum memuat skrip DataTables -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- Inisialisasi DataTables -->
<script>
    $(document).ready(function() {
        $('#table-pay').DataTable({
            paging: true, // Aktifkan paging
            pagingType: 'full_numbers', // Tipe tampilan tombol halaman
            lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
        });
    });
    </script>
@endsection
