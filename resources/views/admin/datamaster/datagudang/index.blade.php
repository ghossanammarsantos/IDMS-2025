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
                                <h4 class="card-title text-white" id="basic-layout-form">Data Gudang</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body p-1">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1" data-toggle="modal" data-target="#default"><i class="fa fa-plus"></i> Add New</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="table-gudang" class="table table-hover table-bordered">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Kode Gudang</th>
                                                    <th>Jenis Gudang</th>
                                                    <th>Nama Gudang</th>
                                                    <th>Luas</th>
                                                    <th>Lokasi</th>
                                                    <th>Alamat</th>
                                                    <th>Status Gudang</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($gudang_list as $data)
                                                <tr>
                                                    <td>{{ $data->kode_gudang }}</td>
                                                    <td>{{ $data->jenis_gudang }}</td>
                                                    <td>{{ $data->nama_gudang }}</td>
                                                    <td>{{ $data->luas }}</td>
                                                    <td>{{ $data->lokasi }}</td>
                                                    <td>{{ $data->alamat }}</td>
                                                    <td>{{ $data->status_gd }}</td>
                                                    <td>
                                                        <!-- <a href="" class="btn btn-sm btn-info">Edit</a> -->
                                                        <form id="deleteForm{{ $data->nama_gudang }}" action="{{ route('gudang.destroy', $data->nama_gudang) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="openDeleteModal('{{ $data->nama_gudang }}')">Hapus</button>
                                                        </form>
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

<!-- Modal Add New -->
<div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header bg-dark">
              <h4 class="modal-title text-white" id="myModalLabel1">Tambah Gudang Baru</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <form action="{{ route('gudang.store') }}" method="POST">
              @csrf
              <div class="modal-body">
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="nama_gudang">Nama Gudang</label>
                              <input type="text" class="form-control" id="nama_gudang" name="nama_gudang" placeholder="Nama Gudang" required>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="jenis_gudang">Jenis Gudang</label>
                              <select class="form-control" id="jenis_gudang" name="jenis_gudang" required>
                                  <option value="">--- Pilih Jenis Gudang ---</option>
                                  <option value="TERBUKA">TERBUKA</option>
                                  <option value="TERTUTUP">TERTUTUP</option>
                              </select>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="luas">Luas</label>
                              <input type="text" class="form-control" id="luas" name="luas" placeholder="Luas Gudang" required>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="lokasi">Lokasi</label>
                              <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Lokasi Gudang" required>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="luas">Alamat</label>
                              <input type="text" class="form-control" id="luas" name="alamat" placeholder="Alamat Gudang" required>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="lokasi">Status Gudang</label>
                              <select class="form-control" id="status_gd" name="status_gd" required>
                                  <option value="">--- Pilih Status Gudang ---</option>
                                  <option value="OK">OK</option>
                                  <option value="FULL">FULL</option>
                              </select>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
          </form>
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
    function openDeleteModal(nama_gudang) {
        $('#confirmDeleteModal').modal('show');
        var form = document.getElementById('deleteForm' + nama_gudang);
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
        $('#table-gudang').DataTable({
            paging: true, // Aktifkan paging
            pagingType: 'full_numbers', // Tipe tampilan tombol halaman
            lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
        });
    });
    </script>
@endsection
