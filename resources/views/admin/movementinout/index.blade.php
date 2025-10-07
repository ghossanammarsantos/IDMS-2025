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
                                    <h4 class="card-title text-white" id="basic-layout-form">Movement IN / OUT</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <!-- <div class="row">
                                            <div class="col-md-5">
                                                <a href="{{ route('eor.create') }}"
                                                    class="btn btn-dark btn-min-width mr-1 mb-1">
                                                    <i class="fa fa-plus"></i> Add New
                                                </a>
                                            </div>
                                        </div> -->
                                        <div class="table-responsive">
                                            <table id="table-eor" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Kode EIRI</th>
                                                        
                                                        <th>No. Container</th>
                                                        <th>Ukuran Container</th>
                                                        <th>Waktu Gate In</th>
                                                        <th>PIC Gate In</th>
                                                        <th>No BL/DO</th>
                                                        <th>Waktu Survey</th>
                                                        <th>PIC Survey</th>
                                                        
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($datalist as $data)
                                                        <tr>
                                                            <td>{{ $data->kode_survey }}</td>
                                                            
                                                            <td>{{ $data->no_container }}</td>
                                                            <td>{{ $data->size_type }}</td>
                                                            <td>{{ $data->gatein_time }}</td>
                                                            <td>{{ $data->pic_gatein }}</td>
                                                            <td>{{ $data->no_bldo }}</td>
                                                           
                                                           
                                                          
                                                            <td>{{ $data->survey_time }}</td>
                                                            <td>{{ $data->pic }}</td>
                                                            
                                                            <td>
                                                                <button class="btn btn-warning btn-sm">
                                                                        <a class="fa fa-eye" href="{{ route('movementinout.show', ['kode_survey' => $data->kode_survey]) }}">
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
</script>
    <!-- Tambahkan jQuery sebelum memuat skrip DataTables -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- Inisialisasi DataTables -->
<script>
    $(document).ready(function() {
        $('#table-eor').DataTable({
            paging: true, // Aktifkan paging
            pagingType: 'full_numbers', // Tipe tampilan tombol halaman
            lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
        });
    });
    </script>
@endsection
