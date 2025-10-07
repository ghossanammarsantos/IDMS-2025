@extends('admin.layouts.app', [
    'activePage' => 'Master',
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
                                    <h4 class="card-title text-white" id="basic-layout-form">Survey Out</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <a href="{{ route('surveyout.create') }}"
                                                    class="btn btn-dark btn-min-width mr-1 mb-1">
                                                    <i class="fa fa-plus"></i> Add New
                                                </a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="table-surveyout" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Kode EIR Out</th>
                                                        <th>No. Container</th>
                                                        <th>Ukuran Container</th>
                                                        <th>Movement</th>
                                                        <th>Kode EIR IN</th>
                                                        <th>No Booking</th>
                                                        <th>No Truck</th>
                                                        <th>Driver</th>
                                                        <th>Waktu Survey Out</th>
                                                        <th>PIC Survey Out</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($surveyout_list as $data)
                                                        <tr>
                                                            <td>{{ $data->kode_surveyout }}</td>
                                                            <td>{{ $data->no_container }}</td>
                                                            <td>{{ $data->size_type }}</td>
                                                            <td>{{ $data->movement }}</td>
                                                            <td>{{ $data->kode_surveyin }}</td>
                                                            <td>{{ $data->no_booking }}</td>
                                                            <td>{{ $data->no_truck }}</td>
                                                            <td>{{ $data->driver }}</td>
                                                            <td>{{ $data->surveyout_time }}</td>
                                                            <td>{{ $data->pic_surveyout }}</td>
                                                            <td>
                                                                <button class="btn btn-black btn-sm">
                                                                    <a class="fa fa-print"
                                                                        href="{{ route('surveyout.cetak_eiro', ['kode_surveyout' => $data->kode_surveyout]) }}">
                                                                        Cetak EIR Out
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
    </div>
    </div>
    <!-- Tambahkan jQuery sebelum memuat skrip DataTables -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js">
    </script>
    <!-- Inisialisasi DataTables -->
    <script>
        $(document).ready(function() {
            $('#table-surveyout').DataTable({
                paging: true, // Aktifkan paging
                pagingType: 'full_numbers', // Tipe tampilan tombol halaman
                lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
            });
        });
    </script>
@endsection
