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
                                    <h4 class="card-title text-white" id="basic-layout-form">Survey In</h4>
                                </div>
                                <!-- <img src="{{ asset('surveyin_photo/' . '1719909356.png') }}" alt="Surveyin Photo"> -->
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <a href="{{ route('surveyin.create') }}"
                                                    class="btn btn-dark btn-min-width mr-1 mb-1">
                                                    <i class="fa fa-plus"></i> Add New
                                                </a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="table-surveyin" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Kode Survey</th>
                                                        <th>No. Container</th>
                                                        <th>Ukuran Container</th>
                                                        <th>Waktu Gate In</th>
                                                        <th>PIC Gate In</th>
                                                        <th>Status Container</th>
                                                        <th>Grade Container</th>
                                                        <th>No BL/DO</th>
                                                        <th>No Truck</th>
                                                        <th>Driver</th>
                                                        <th>Service</th>
                                                        <th>Waktu Survey</th>
                                                        <th>PIC Survey</th>
                                                        <th>Lokasi Penumpukan</th>
                                                        <th>Status WO</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($survey_list as $data)
                                                        <tr>
                                                            <td>{{ $data->kode_survey }}</td>
                                                            <td>{{ $data->no_container }}</td>
                                                            <td>{{ isset($data->size_type) ? $data->size_type : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->gatein_time) ? $data->gatein_time : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->pic_gatein) ? $data->pic_gatein : '-' }}
                                                            </td>
                                                            <td>{{ $data->status_container }}</td>
                                                            <td>{{ $data->grade_container }}</td>
                                                            <td>{{ $data->no_bldo }}</td>
                                                            <td>{{ $data->no_truck }}</td>
                                                            <td>{{ $data->driver }}</td>
                                                            <td>{{ $data->kegiatan }}</td>
                                                            <td>{{ $data->survey_time }}</td>
                                                            <td>{{ $data->pic }}</td>
                                                            <td>B:{{ $data->block }} S:{{ $data->slot }}
                                                                R:{{ $data->row2 }} T:{{ $data->tier }}</td>
                                                            <td>{{ $data->status_wo }}</td>
                                                            <td>
                                                                <button class="btn btn-black btn-sm">
                                                                    <a class="fa fa-print"
                                                                        href="{{ route('surveyin.cetak_eir', ['kode_survey' => $data->kode_survey]) }}">
                                                                        Cetak EIR
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
@endsection
<!-- Tambahkan jQuery sebelum memuat skrip DataTables -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js">
</script>
<!-- Inisialisasi DataTables -->
<script>
    $(document).ready(function() {
        $('#table-surveyin').DataTable({
            paging: true, // Aktifkan paging
            pagingType: 'full_numbers', // Tipe tampilan tombol halaman
            lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
        });
    });
</script>
