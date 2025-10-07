@extends('admin.layouts.app', [
    'activePage' => 'Master',
])
<style>
    /* Hilangkan horizontal scroll hanya di modal #addNew */
    #addNew .modal-dialog,
    #addNew .modal-content,
    #addNew .modal-body {
        overflow-x: hidden;
    }

    /* Antisipasi .row bootstrap yg kadang bikin overflow jika layout sempit */
    #addNew .modal-body .row {
        margin-left: 0;
        margin-right: 0;
    }

    /* Pastikan Select2 di modal tidak melebar melebihi kontainer */
    #addNew .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* (Opsional) kalau body suka muncul scrollbar horizontal saat modal dibuka */
    body.modal-open {
        overflow-x: hidden;
    }
</style>
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Announcement Import</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1"
                                                    data-toggle="modal" data-target="#addNew"><i class="fa fa-plus"></i> add
                                                    New</button>
                                                <button type="button" class="btn btn-secondary btn-min-width mr-1 mb-1"
                                                    id="importExcelBtn" data-toggle="modal"
                                                    data-target="#importExcelModal"><i class="fa fa-file"></i> Import In
                                                    Excel</button>
                                            </div>
                                        </div>
                                        <!-- <button type="button" class="btn btn-info btn-min-width mr-1 mb-1">Add New</button> -->
                                        <div class="table-responsive">
                                            @if (session('success'))
                                                <div class="alert alert-success">{{ session('success') }}</div>
                                            @endif

                                            @if (session('error'))
                                                <div class="alert alert-danger">{{ session('error') }}</div>
                                            @endif

                                            @if (session('import_summary'))
                                                @php $sum = session('import_summary'); @endphp
                                                <div class="alert alert-info">
                                                    <strong>Ringkasan Import:</strong>
                                                    <div>Berhasil: {{ $sum['sukses'] ?? 0 }}, Gagal:
                                                        {{ $sum['gagal'] ?? 0 }}</div>

                                                    @if (!empty($sum['detail']))
                                                        <hr>
                                                        <details>
                                                            <summary>Lihat detail baris yang gagal</summary>
                                                            <ul class="mt-2">
                                                                @foreach ((array) ($sum['detail'] ?? []) as $f)
                                                                    <li>
                                                                        Baris {{ $f['baris'] ?? ($f['row'] ?? '-') }}
                                                                        ({{ $f['container'] ?? ($f['no_container'] ?? '-') }})
                                                                        :
                                                                        {{ $f['pesan'] ?? ($f['message'] ?? 'Terjadi kesalahan.') }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </details>
                                                    @endif
                                                </div>
                                            @endif

                                            <table id="table-annimport" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Customer Code</th>
                                                        <th>No. Container</th>
                                                        <th>No. BLDO</th>
                                                        <th>Consignee</th>
                                                        <th>Ukuran Container</th>
                                                        <th>Ex Vessel</th>
                                                        <th>Tgl Berthing</th>
                                                        <th>Remarks</th>
                                                        <th>Status Survey</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($annimport_list as $data)
                                                        <tr>
                                                            <td>{{ $data->customer_code }}</td>
                                                            <td>{{ $data->no_container }}</td>
                                                            <td>{{ $data->no_bldo }}</td>
                                                            <td>{{ isset($data->consignee) ? $data->consignee : '-' }}</td>
                                                            <td>{{ isset($data->size_type) ? $data->size_type : '-' }}</td>
                                                            <td>{{ $data->ex_vessel }}</td>
                                                            <td></td>
                                                            <td>{{ $data->remarks }}</td>
                                                            <td>{{ $data->status_surveyin }}</td>
                                                            <td>
                                                                @php
                                                                    // Buat ID unik & aman untuk modal (hindari karakter spesial dari no_container)
                                                                    $modalId = 'editAnnImport_' . $loop->index;
                                                                @endphp
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    data-toggle="modal" data-target="#{{ $modalId }}">
                                                                    Edit
                                                                </button>
                                                                <form
                                                                    action="{{ route('annimport.destroy', $data->no_container) }}"
                                                                    method="POST" style="display:inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger btn-sm">Hapus</button>
                                                                </form>
                                                            </td>
                                                        </tr>

                                                        <!-- Modal Edit -->
                                                        <div class="modal fade" id="{{ $modalId }}" tabindex="-1"
                                                            role="dialog" aria-labelledby="{{ $modalId }}Label"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-lg" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-dark">
                                                                        <h4 class="modal-title text-white"
                                                                            id="{{ $modalId }}Label">Edit Announcement
                                                                            Import</h4>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>

                                                                    <form class="form"
                                                                        action="{{ route('annimport.update', $data->no_container) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')

                                                                        <input type="hidden" name="original_no_container"
                                                                            value="{{ $data->no_container }}">

                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                {{-- Customer Code --}}
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="customer_code_{{ $loop->index }}">Customer
                                                                                            Code</label>
                                                                                        <input type="text"
                                                                                            id="customer_code_{{ $loop->index }}"
                                                                                            name="customer_code"
                                                                                            class="form-control"
                                                                                            value="{{ old('customer_code', $data->customer_code) }}"
                                                                                            placeholder="Masukkan customer code">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- No. Container (readonly jika dijadikan primary key) --}}
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="no_container_{{ $loop->index }}">No.
                                                                                            Container</label>
                                                                                        <input type="text"
                                                                                            id="no_container_{{ $loop->index }}"
                                                                                            name="no_container"
                                                                                            class="form-control"
                                                                                            value="{{ old('no_container', $data->no_container) }}"
                                                                                            @if (true) readonly @endif>
                                                                                        {{-- Jika tidak readonly, hapus atribut readonly di atas --}}
                                                                                    </div>
                                                                                </div>

                                                                                {{-- No. BLDO --}}
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="no_bldo_{{ $loop->index }}">No.
                                                                                            BLDO</label>
                                                                                        <input type="text"
                                                                                            id="no_bldo_{{ $loop->index }}"
                                                                                            name="no_bldo"
                                                                                            class="form-control"
                                                                                            value="{{ old('no_bldo', $data->no_bldo) }}"
                                                                                            placeholder="Masukkan nomor BL/DO">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Consignee --}}
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="consignee_{{ $loop->index }}">Consignee</label>
                                                                                        <input type="text"
                                                                                            id="consignee_{{ $loop->index }}"
                                                                                            name="consignee"
                                                                                            class="form-control"
                                                                                            value="{{ old('consignee', $data->consignee) }}"
                                                                                            placeholder="Masukkan consignee">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Ukuran / Size Type --}}
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="size_type_{{ $loop->index }}">Ukuran
                                                                                            Container (Size Type)</label>
                                                                                        <select
                                                                                            class="form-control select2 size_type-select"
                                                                                            id="size_type_{{ $loop->index }}"
                                                                                            name="size_type"
                                                                                            data-placeholder="-- Pilih Ukuran --"
                                                                                            data-dropdown-parent="#{{ $modalId }}">
                                                                                            <option value="">
                                                                                            </option>
                                                                                            @foreach ($container as $row)
                                                                                                <option
                                                                                                    value="{{ $row->ukuran_container }}"
                                                                                                    @selected(old('size_type', $data->size_type) == $row->ukuran_container)>
                                                                                                    {{ $row->ukuran_container }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Ex Vessel --}}
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="ex_vessel_{{ $loop->index }}">Ex
                                                                                            Vessel</label>
                                                                                        <input type="text"
                                                                                            id="ex_vessel_{{ $loop->index }}"
                                                                                            name="ex_vessel"
                                                                                            class="form-control"
                                                                                            value="{{ old('ex_vessel', $data->ex_vessel) }}"
                                                                                            placeholder="Masukkan ex vessel">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Tgl Berthing --}}
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="tanggal_berthing_{{ $loop->index }}">Tgl
                                                                                            Berthing</label>
                                                                                        <input type="date"
                                                                                            id="tanggal_berthing_{{ $loop->index }}"
                                                                                            name="tanggal_berthing"
                                                                                            class="form-control"
                                                                                            value="{{ old('tanggal_berthing', $data->tanggal_berthing ?? '') }}">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Remarks --}}
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="remarks_{{ $loop->index }}">Remarks</label>
                                                                                        <input type="text"
                                                                                            id="remarks_{{ $loop->index }}"
                                                                                            name="remarks"
                                                                                            class="form-control"
                                                                                            value="{{ old('remarks', $data->remarks) }}"
                                                                                            placeholder="Catatan">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Status Survey --}}
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="status_survey_{{ $loop->index }}">Status
                                                                                            Survey</label>
                                                                                        <select
                                                                                            id="status_survey_{{ $loop->index }}"
                                                                                            name="status_survey"
                                                                                            class="form-control">
                                                                                            <option value="OPEN"
                                                                                                @selected(old('status_survey', $data->status_survey) == 'OPEN')>
                                                                                                OPEN</option>
                                                                                            <option value="CLOSE"
                                                                                                @selected(old('status_survey', $data->status_survey) == 'CLOSE')>
                                                                                                CLOSE</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-outline-secondary"
                                                                                data-dismiss="modal">Close</button>
                                                                            <button type="submit"
                                                                                class="btn btn-outline-dark">Save
                                                                                changes</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="modal fade" id="addNew" tabindex="-1" role="dialog"
                                            aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-dark">
                                                        <h4 class="modal-title text-white" id="myModalLabel1">Add New
                                                            Announcement Import</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>

                                                    <form class="form" action="{{ route('annimport.store') }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                {{-- Customer Code --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="customer_code">Customer Code</label>
                                                                        <input type="text" id="customer_code"
                                                                            name="customer_code" class="form-control"
                                                                            placeholder="Masukkan customer code"
                                                                            value="{{ old('customer_code') }}">
                                                                    </div>
                                                                </div>

                                                                {{-- No. Container --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="no_container">No. Container</label>
                                                                        <input type="text" id="no_container"
                                                                            name="no_container" class="form-control"
                                                                            placeholder="Masukkan nomor container"
                                                                            value="{{ old('no_container') }}">
                                                                    </div>
                                                                </div>

                                                                {{-- No. BLDO --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="no_bldo">No. BLDO</label>
                                                                        <input type="text" id="no_bldo"
                                                                            name="no_bldo" class="form-control"
                                                                            placeholder="Masukkan nomor BL/DO"
                                                                            value="{{ old('no_bldo') }}">
                                                                    </div>
                                                                </div>

                                                                {{-- Consignee --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="consignee">Consignee</label>
                                                                        <input type="text" id="consignee"
                                                                            name="consignee" class="form-control"
                                                                            placeholder="Masukkan consignee"
                                                                            value="{{ old('consignee') }}">
                                                                    </div>
                                                                </div>

                                                                {{-- Ukuran / Size Type (ambil dari tabel container) --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="size_type">Ukuran Container (Size
                                                                            Type)</label>
                                                                        <select class="form-control select2"
                                                                            id="size_type" name="size_type"
                                                                            data-placeholder="-- Pilih Ukuran --"
                                                                            data-dropdown-parent="#addNew">
                                                                            <option value=""></option>
                                                                            @foreach ($container as $row)
                                                                                <option
                                                                                    value="{{ $row->ukuran_container }}"
                                                                                    {{ old('size_type') == $row->ukuran_container ? 'selected' : '' }}>
                                                                                    {{ $row->ukuran_container }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Ex Vessel --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="ex_vessel">Ex Vessel</label>
                                                                        <input type="text" id="ex_vessel"
                                                                            name="ex_vessel" class="form-control"
                                                                            placeholder="Masukkan ex vessel"
                                                                            value="{{ old('ex_vessel') }}">
                                                                    </div>
                                                                </div>

                                                                {{-- Voyage --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="voyage">Voyage</label>
                                                                        <input type="text" id="voyage"
                                                                            name="voyage" class="form-control"
                                                                            placeholder="Masukkan voyage"
                                                                            value="{{ old('voyage') }}">
                                                                    </div>
                                                                </div>

                                                                {{-- Tgl Berthing --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="tanggal_berthing">Tgl Berthing</label>
                                                                        <input type="date" id="tanggal_berthing"
                                                                            name="tanggal_berthing" class="form-control"
                                                                            value="{{ old('tanggal_berthing') }}">
                                                                    </div>
                                                                </div>

                                                                {{-- Remarks --}}
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="remarks">Remarks</label>
                                                                        <input type="text" id="remarks"
                                                                            name="remarks" class="form-control"
                                                                            placeholder="Catatan"
                                                                            value="{{ old('remarks') }}">
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-outline-dark">Save
                                                                changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- End Modal -->

                                        <!-- Modal for Import Excel -->
                                        <div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog"
                                            aria-labelledby="importExcelModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="importExcelModalLabel">Import Data
                                                            from Excel</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('annimport.importExcel') }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="excelFile">Choose Excel File:</label>
                                                                <input type="file" class="form-control-file"
                                                                    id="excelFile" name="excel_file">
                                                                @error('excel_file')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Import
                                                                Data</button>
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
                </section>
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
            $('#table-annimport').DataTable({
                paging: true, // Aktifkan paging
                pagingType: 'full_numbers', // Tipe tampilan tombol halaman
                lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
            });
        });
    </script>

    <script>
        (function() {
            function initAllSizeTypeSelect2() {
                jQuery('.size_type-select').each(function() {
                    var $el = jQuery(this);
                    var dp = $el.data('dropdown-parent') || null;
                    $el.select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: $el.data('placeholder') || '-- Pilih Ukuran --',
                        allowClear: true,
                        dropdownParent: dp ? jQuery(dp) : null
                    });
                });

                // Fokuskan search saat dropdown dibuka (nyaman di modal)
                jQuery(document).on('select2:open', function() {
                    var s = document.querySelector('.select2-container--open .select2-search__field');
                    if (s) {
                        s.focus();
                    }
                });
            }

            if (window.jQuery) {
                jQuery(function() {
                    initAllSizeTypeSelect2();
                });
            } else {
                document.addEventListener('DOMContentLoaded', initAllSizeTypeSelect2);
            }
        })();
    </script>

@endsection
