@extends('admin.layouts.app', [
    'activePage' => 'Master',
])

@section('content')
<div class="app-content content">
  <div class="content-wrapper">
      <!-- <div class="content-header row">
        <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
          <h3 class="content-header-title mb-0 d-inline-block">Warehouse</h3>
          <div class="row breadcrumbs-top d-inline-block">
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item">Workorder</li>
                <li class="breadcrumb-item">List</li>
              </ol>
            </div>
          </div>
        </div>
      </div> -->
      <div class="content-body">
        <section id="basic-form-layouts">
          <div class="row">
            <div class="col-md-12">
                    
              <div class="card">
                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                  <h4 class="card-title text-white" id="basic-layout-form">Data Container</h4>
                </div>
                <div class="card-content">
                  <div class="card-body p-1">
                    <div class="row">
                      <div class="col-md-5">
                        <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1" data-toggle="modal" data-target="#default"><i class="fa fa-plus"></i> Add New</button>
                      </div>
                    </div>
                    <!-- <button type="button" class="btn btn-info btn-min-width mr-1 mb-1">Add New</button> -->
                    <div class="table-responsive">
                        <table id="table-cont" class="table table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nama Container</th>
                                    <th>Jenis Container</th>
                                    <th>Ukuran Container</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                              @foreach($container_list as $data)
                                <tr>
                                    <td>{{ $data->nama_container }}</td>
                                    <td>{{ $data->jenis_container }}</td>
                                    <td>{{ $data->ukuran_container }}</td>
                                    <td>
                                            <!-- <a href="" class="btn btn-sm btn-info">Edit</a> -->
                                            <form id="deleteForm{{ $data->nama_container }}" action="{{ route('container.destroy', $data->nama_container) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="openDeleteModal('{{ $data->nama_container }}')">Hapus</button>
                                        </form>
                                        </td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                      <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                              <div class="modal-header bg-dark">
                                  <h4 class="modal-title text-white" id="myModalLabel1">Add New Container</h4>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  <form method="POST" action="{{ route('container.store') }}">
                                      @csrf
                                      <div class="form-group">
                                          <label for="nama">Nama Container</label>
                                          <input type="text" id="nama" class="form-control" placeholder="Nama Container" name="nama_container">
                                      </div>
                                      <div class="form-group">
                                          <label for="jns">Jenis Container</label>
                                          <input type="text" id="jns" class="form-control" placeholder="Jenis Container" name="jenis_container">
                                      </div>
                                      <div class="form-group">
                                          <label for="ukuran">Ukuran Container</label>
                                          <input type="text" id="ukuran" class="form-control" placeholder="Ukuran Container" name="ukuran_container">
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                                          <button type="submit" class="btn btn-primary">Save changes</button>
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
        $('#table-cont').DataTable({
            paging: true, // Aktifkan paging
            pagingType: 'full_numbers', // Tipe tampilan tombol halaman
            lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
        });
    });
    </script>
@endsection