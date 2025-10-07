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
                                    <h4 class="card-title text-white" id="basic-layout-form">Data Cedex</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1"
                                                    data-toggle="modal" data-target="#default"><i class="fa fa-plus"></i>
                                                    Add New</button>
                                                <button type="button" class="btn btn-secondary btn-min-width mr-1 mb-1"
                                                    id="importExcelBtn" data-toggle="modal"
                                                    data-target="#importExcelModal"><i class="fa fa-file"></i> Import Cedex</button>
                                            </div>
                                        </div>
                                        <!-- <button type="button" class="btn btn-info btn-min-width mr-1 mb-1">Add New</button> -->
                                        <div class="table-responsive">
                                            <table id="table-cedex" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Cedex</th>
                                                        <th>Deskripsi</th>
                                                        <th>Group</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($cedex_list as $data)
                                                        <tr>
                                                            <td>{{ $data->cedex_code }}</td>
                                                            <td>{{ $data->deskripsi }}</td>
                                                            <td>{{ $data->grup }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="modal fade text-left" id="default" tabindex="-1" role="dialog"
                                            aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-dark">
                                                        <h4 class="modal-title text-white" id="myModalLabel1">Add New Tarif
                                                        </h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('cedex.store') }}" method="POST"
                                                            class="form">
                                                            @csrf
                                                            <div class="form-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="cedex">Cedex</label>
                                                                            <input type="text" id="cedex"
                                                                                class="form-control" placeholder="Cedex"
                                                                                name="cedex_code">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="deskripsi">Deskripsi</label>
                                                                            <input type="text" id="deskripsi"
                                                                                class="form-control"
                                                                                placeholder="Deskripsi" name="deskripsi">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="grup">Group</label>
                                                                            <input type="text" id="grup"
                                                                                class="form-control" placeholder="Group"
                                                                                name="grup">
                                                                        </div>
                                                                    </div>    
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                    class="btn grey btn-outline-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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
    <!-- Tambahkan jQuery sebelum memuat skrip DataTables -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- Inisialisasi DataTables -->
<script>
    $(document).ready(function() {
        $('#table-cedex').DataTable({
            paging: true, // Aktifkan paging
            pagingType: 'full_numbers', // Tipe tampilan tombol halaman
            lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
        });
    });
    </script>
@endsection
