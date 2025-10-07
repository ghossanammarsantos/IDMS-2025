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
                                    <h4 class="card-title text-white" id="basic-layout-form">Data Tarif</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1"
                                                    data-toggle="modal" data-target="#default"><i class="fa fa-plus"></i>
                                                    Add New</button>
                                            </div>
                                        </div>
                                        <!-- <button type="button" class="btn btn-info btn-min-width mr-1 mb-1">Add New</button> -->
                                        <div class="table-responsive">
                                            <table id="table-tarif" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Nama Jasa</th>
                                                        <th>Cedex</th>
                                                        <th>Deskripsi</th>
                                                        <th>Group</th>
                                                        <th>Tarif</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($tarif_list as $data)
                                                        <tr>
                                                            <td>{{ $data->nama_jasa }}</td>
                                                            <td>{{ $data->cedex }}</td>
                                                            <td>{{ $data->deskripsi }}</td>
                                                            <td>{{ $data->grup }}</td>
                                                            <td>{{ $data->tarif }}</td>
                                                            <td>
                                                                <!-- <a href="" class="btn btn-sm btn-info">Edit</a> -->
                                                                <form id="deleteForm{{ $data->nama_jasa }}" action="{{ route('tarif.destroy', $data->nama_jasa) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn btn-sm btn-danger" onclick="openDeleteModal('{{ $data->nama_jasa }}')">Hapus</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <ul class="pagination pagination-separate pagination-curved firstLast2-links">
                                            <li class="page-item first disabled"><a href="#"
                                                    class="page-link bg-light">First</a></li>
                                            <li class="page-item prev disabled"><a href="#"
                                                    class="page-link bg-light">Prev</a></li>
                                            <li class="page-item active"><a href="#" class="page-link">1</a></li>
                                            <li class="page-item"><a href="#" class="page-link">2</a></li>
                                            <li class="page-item"><a href="#" class="page-link">3</a></li>
                                            <li class="page-item"><a href="#" class="page-link">4</a></li>
                                            <li class="page-item next"><a href="#" class="page-link">Next</a></li>
                                            <li class="page-item last"><a href="#" class="page-link">Last</a></li>
                                        </ul>
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
                                                        <form action="{{ route('tarif.store') }}" method="POST"
                                                            class="form">
                                                            @csrf
                                                            <div class="form-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="nama_jasa">Nama Jasa</label>
                                                                            <input type="text" id="nama_jasa"
                                                                                class="form-control" placeholder="Nama Jasa"
                                                                                name="nama_jasa">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="cedex">Cedex</label>
                                                                            <input type="text" id="cedex"
                                                                                class="form-control" placeholder="Cedex"
                                                                                name="cedex">
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
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="tarif">Tarif</label>
                                                                            <input type="number" id="tarif"
                                                                                class="form-control" placeholder="Tarif"
                                                                                name="tarif">
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

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger white">
                <h4 class="modal-title" id="myModalLabel1">Konfirmasi Hapus</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- JavaScript untuk menampilkan modal konfirmasi -->
<script>
    function openDeleteModal(nama_kapal) {
        $('#confirmDeleteModal').modal('show');
        var form = document.getElementById('deleteForm' + nama_kapal);
        var deleteUrl = form.getAttribute('action');
        var deleteForm = document.getElementById('deleteForm');
        deleteForm.setAttribute('action', deleteUrl);
    }
</script>
    <!-- Tambahkan jQuery sebelum memuat skrip DataTables -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- Inisialisasi DataTables -->
<script>
    $(document).ready(function() {
        $('#table-tarif').DataTable({
            paging: true, // Aktifkan paging
            pagingType: 'full_numbers', // Tipe tampilan tombol halaman
            lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
        });
    });
    </script>
@endsection
