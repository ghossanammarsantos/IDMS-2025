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
                  <h4 class="card-title text-white" id="basic-layout-form">Announcement Export</h4>
                </div>
                <div class="card-content">
                  <div class="card-body p-1">
                    <div class="row">
                      <div class="col-md-5">
                        <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1" data-toggle="modal" data-target="#in"><i class="fa fa-sign-in"></i> IN</button>
                        <button type="button" class="btn btn-secondary btn-min-width mr-1 mb-1" id="importExcelBtn" data-toggle="modal" data-target="#importExcelModal"><i class="fa fa-file"></i> Import In Excel</button>
                        <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1" data-toggle="modal" data-target="#out"><i class="fa fa-sign-out"></i> OUT</button>
                      </div>
                    </div>
                    <!-- <button type="button" class="btn btn-info btn-min-width mr-1 mb-1">Add New</button> -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No. Container</th>
                                    <th>Jenis Container</th>
                                    <th>Ukuran Container</th>
                                    <th>Status Survey</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                              @foreach($annimport_list as $data)
                              <tr>
                                      <td>{{ $data->no_container }}</td>
                                      <td>{{ isset($data->jenis_container) ? $data->jenis_container : '-' }}</td>
                                      <td>{{ isset($data->ukuran_container) ? $data->ukuran_container : '-' }}</td>
                                      <td>{{ $data->status_survey }}</td>
                                      <td>
                                      <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editModal" data-no_container="{{ $data->no_container }}">Edit</button>                                        <!-- Tombol hapus di sini -->
                                        <form action="{{ route('annimport.destroy', $data->no_container) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                      </td>
                                  </tr>
                              @endforeach
                          </tbody>

                        </table>
                    </div>
                    <ul class="pagination pagination-separate pagination-curved firstLast2-links">
                      <li class="page-item first disabled"><a href="#" class="page-link bg-light">First</a></li>
                      <li class="page-item prev disabled"><a href="#" class="page-link bg-light">Prev</a></li>
                      <li class="page-item active"><a href="#" class="page-link">1</a></li>
                      <li class="page-item"><a href="#" class="page-link">2</a></li>
                      <li class="page-item"><a href="#" class="page-link">3</a></li>
                      <li class="page-item"><a href="#" class="page-link">4</a></li>
                      <li class="page-item next"><a href="#" class="page-link">Next</a></li>
                      <li class="page-item last"><a href="#" class="page-link">Last</a></li>
                    </ul>
                    <div class="modal fade text-left" id="in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                      <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                              <div class="modal-header bg-dark" style="border-radius:0px;">
                                  <h4 class="modal-title text-white" id="myModalLabel1">Add New Announcement Import</h4>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  <form class="form" action="{{ route('annimport.store') }}" method="POST">
                                      @csrf
                                      <div class="form-body">
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label for="no_container">Nomor Container</label>
                                                      <input type="text" id="no_container" class="form-control" placeholder="masukkan nomor container" name="no_container">
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label for="jenis_container">Jenis Container</label>
                                                      <select class="single-select-box selectivity-input" id="jenis_container" data-placeholder="No jenis container selected" name="jenis_container">
                                                          <option value="">-- Pilih Jenis Container --</option>
                                                          @foreach($container as $row)
                                                          <option value="{{ $row->jenis_container }}">{{ $row->jenis_container }}</option>
                                                          @endforeach
                                                      </select>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label for="ukuran_container">Ukuran Container</label>
                                                      <select class="single-select-box selectivity-input" id="ukuran_container" data-placeholder="No Ukuran container selected" name="ukuran_container">
                                                          <option value="">-- Pilih Ukuran Container --</option>
                                                          @foreach($container as $row)
                                                          <option value="{{ $row->ukuran_container }}">{{ $row->ukuran_container }}</option>
                                                          @endforeach
                                                      </select>
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label for="status">Status Survey</label>
                                                      <select class="single-select-box selectivity-input" id="status" data-placeholder="No tarif selected" name="status_survey">
                                                          <option value="">-- Pilih Status --</option>
                                                          <option value="OPEN">OPEN</option>
                                                          <option value="CLOSE">CLOSE</option>
                                                      </select>
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

  <!-- End Modal -->

                                        <!-- Modal for Import Excel -->
                    <div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="importExcelModalLabel">Import Data from Excel</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('annimport.importExcel') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="excelFile">Choose Excel File:</label>
                                            <input type="file" class="form-control-file" id="excelFile" name="excel_file">
                                            @error('excel_file')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Edit -->
                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-dark" style="border-radius:0px;">
                                    <h4 class="modal-title text-white" id="editModalLabel">Edit Status Survey</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" method="POST" action="{{ route('annimport.updateStatus', ['no_container' => $data->no_container]) }}">
                                        @csrf
                                        <input type="hidden" id="edit_annimport_id" name="no_container">
                                        <div class="form-group">
                                            <label for="edit_status">Status Survey</label>
                                            <select class="form-control" id="edit_status" name="status_survey">
                                                <option value="OPEN">OPEN</option>
                                                <option value="CLOSE">CLOSE</option>
                                            </select>
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

<script>
    $(document).ready(function() {
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var no_container = button.data('no_container');
            var modal = $(this);
            modal.find('.modal-body #edit_annimport_id').val(no_container);
        });
    });
</script>
@endsection