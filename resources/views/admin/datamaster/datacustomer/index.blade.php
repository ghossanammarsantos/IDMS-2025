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
                  <h4 class="card-title text-white" id="basic-layout-form">Data Customer</h4>
                </div>
                <div class="card-content">
                  <div class="card-body p-1">
                    <div class="row">
                      <div class="col-md-5">
                        <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1" data-toggle="modal" data-target="#default"><i class="fa fa-plus"></i> Add New</button>
                      </div>
                    </div>
                    <div class="table-responsive">
                        <table id="table-cust" class="table table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Kode Customer</th>
                                    <th>Nama Customer</th>
                                    <th>Alamat</th>
                                    <th>Kota</th>
                                    <th>Negara</th>
                                    <th>Kategori Customer</th>
                                    <th>Tanggal Bergabung</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                              @foreach($customer_list as $data)
                                <tr>
                                    <td>{{ $data->kode_customer }}</td>
                                    <td>{{ $data->nama_customer }}</td>
                                    <td>{{ $data->alamat }}</td>
                                    <td>{{ $data->kota }}</td>
                                    <td>{{ $data->negara }}</td>
                                    <td>{{ $data->kategori_customer }}</td>
                                    <td>{{ $data->tgl_bergabung }}</td>
                                    <td>
                                        <!-- <a href="" class="btn btn-sm btn-info">Edit</a> -->
                                        <form id="deleteForm{{ $data->nama_customer }}" action="{{ route('customer.destroy', $data->nama_customer) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="openDeleteModal('{{ $data->nama_customer }}')">Hapus</button>
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
                                    <h4 class="modal-title text-white" id="myModalLabel1">Add New Customer</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('customer.store') }}" method="POST" class="form">
                                        @csrf
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="nama_customer">Nama Customer</label>
                                                        <input type="text" id="nama_customer" class="form-control" placeholder="Nama Customer" name="nama_customer" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="alamat">Alamat</label>
                                                        <input type="text" id="alamat" class="form-control" placeholder="Alamat" name="alamat" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="kota">Kota</label>
                                                        <select class="single-select-box selectivity-input" id="single-select-box" data-placeholder="No customer category selected" name="kota" style="padding-left:10px;border:1px solid silver;" required>
                                                            <option value="">--- Pilih Kota Customer ---</option>
                                                            <option value="Batam">Batam</option>
                                                            <option value="Jakarta">Jakarta</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="negara">Negara</label>
                                                        <select class="single-select-box selectivity-input" id="single-select-box" data-placeholder="No customer category selected" name="negara" style="padding-left:10px;border:1px solid silver;" required>
                                                            <option value="">--- Pilih Negara Customer ---</option>
                                                            <option value="Indonesia">Indonesia</option>
                                                            <option value="Singapura">Singapura</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="kategori_customer">Kategori Customer</label>
                                                        <select class="single-select-box selectivity-input" id="single-select-box" data-placeholder="No customer category selected" name="kategori_customer" style="padding-left:10px;border:1px solid silver;" required>
                                                            <option value="">--- Pilih Kategori Customer ---</option>
                                                            <option value="Biasa">Biasa</option>
                                                            <option value="Umum">Umum</option>
                                                            <option value="Kontrak">Kontrak</option>
                                                            <option value="Jasa Transportasi">Jasa Transportasi</option>
                                                            <option value="Khusus">Khusus</option>
                                                            <option value="Madya">Madya</option>
                                                            <option value="Utama">Utama</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="tanggal_bergabung">Tanggal Bergabung</label>
                                                        <input type="date" id="tgl_bergabung" name="tgl_bergabung" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-outline-dark">Save changes</button>
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
        $('#table-cust').DataTable({
            paging: true, // Aktifkan paging
            pagingType: 'full_numbers', // Tipe tampilan tombol halaman
            lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
        });
    });
    </script>
@endsection
