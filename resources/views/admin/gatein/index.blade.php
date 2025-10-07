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
                                    <h4 class="card-title text-white" id="basic-layout-form">Gate In</h4>
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
                                            <table id="table-gatein" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>No. Container</th>
                                                        <th>Ukuran Container</th>
                                                        <th>No BL/DO</th>
                                                        <th>Waktu Gate In</th>
                                                        <th>PIC</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($gatein_list as $data)
                                                        <tr>
                                                            <td>{{ $data->no_container }}</td>
                                                            <td>{{ isset($data->size_type) ? $data->size_type : '-' }}</td>
                                                            <td>{{ $data->no_bldo }}</td>
                                                            <td>{{ $data->gatein_time }}</td>
                                                            <td>{{ $data->pic_gatein }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal fade text-left" id="default" tabindex="-1" role="dialog"
                                            aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-dark" style="border-radius:0px;">
                                                        <h4 class="modal-title text-white" id="myModalLabel1">Add IN</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="addSurveyForm" method="post"
                                                            action="{{ route('gatein.store') }}">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label for="noContainerSelect">Pilih No. Container</label>
                                                                <select class="form-control select2" id="noContainerSelect"
                                                                    name="no_container"
                                                                    data-placeholder="--- Pilih No Container ---"
                                                                    data-ajax-url="{{ route('gatein.select2') }}"
                                                                    data-dropdown-parent="#default" {{-- ID modal Anda --}}
                                                                    data-old='@json(old('no_container'))'>
                                                                    <option value=""></option>
                                                                    {{-- opsi dimuat via AJAX --}}
                                                                </select>
                                                            </div>

                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Modal -->
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
            $('#table-gatein').DataTable({
                paging: true, // Aktifkan paging
                pagingType: 'full_numbers', // Tipe tampilan tombol halaman
                lengthMenu: [5, 10, 25, 50, 100], // Jumlah data yang ditampilkan per halaman
            });
        });
    </script>

    <script>
        (function() {
            function initSelect2() {
                var $el = $('#noContainerSelect');
                var ajaxUrl = $el.data('ajax-url');
                var dp = $el.data('dropdown-parent'); // e.g. "#default"
                var opts = {
                    placeholder: $el.data('placeholder'),
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 2,
                    ajax: {
                        url: ajaxUrl,
                        dataType: 'json',
                        delay: 200,
                        data: function(params) {
                            return {
                                q: (params.term || ''),
                                page: (params.page || 1)
                            };
                        },
                        processResults: function(data, params) {
                            return {
                                results: data.items,
                                pagination: {
                                    more: (data.more || false)
                                }
                            };
                        },
                        cache: true
                    }
                };

                // jika select berada di dalam modal, set dropdownParent
                if (dp) {
                    opts.dropdownParent = $(dp);
                }

                $el.select2(opts);

                // Pastikan input search langsung fokus saat dropdown dibuka
                $el.on('select2:open', function() {
                    var s = document.querySelector('.select2-container--open .select2-search__field');
                    if (s) {
                        s.focus();
                    }
                });

                // Preselect old value (tanpa Blade logic di <script>)
                try {
                    var oldVal = JSON.parse($el.attr('data-old') || 'null');
                    if (oldVal) {
                        var opt = new Option(oldVal, oldVal, true, true);
                        $el.append(opt).trigger('change');
                    }
                } catch (e) {}
            }

            if (window.jQuery) {
                jQuery(function() {
                    initSelect2();
                });
            } else {
                document.addEventListener('DOMContentLoaded', initSelect2);
            }
        })();
    </script>
@endsection
