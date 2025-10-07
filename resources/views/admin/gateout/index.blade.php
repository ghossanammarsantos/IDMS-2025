@extends('admin.layouts.app', ['activePage' => 'Master'])

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Gate Out</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">

                                        {{-- Flash message --}}
                                        @if (session('success'))
                                            <div class="alert alert-success">{{ session('success') }}</div>
                                        @endif
                                        @if (session('error'))
                                            <div class="alert alert-danger">{{ session('error') }}</div>
                                        @endif
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="row mb-1">
                                            <div class="col-md-5">
                                                <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1"
                                                    data-toggle="modal" data-target="#default"><i class="fa fa-plus"></i>
                                                    Add New
                                                </button>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table id="table-gateout" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>No. Container</th>
                                                        <th>Waktu Gate Out</th>
                                                        <th>PIC</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($gateout_list as $data)
                                                        <tr>
                                                            <td>{{ $data->no_container }}</td>
                                                            <td>{{ $data->gateout_time }}</td>
                                                            <td>{{ $data->pic_gateout }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Modal --}}
                                        <div class="modal fade text-left" id="default" tabindex="-1" role="dialog"
                                            aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-dark" style="border-radius:0px;">
                                                        <h4 class="modal-title text-white" id="myModalLabel1">Add OUT</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="addGateOutForm" method="post"
                                                            action="{{ route('gateout.store') }}">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label for="noContainerSelect">Pilih No. Container</label>
                                                                <select class="form-control select2" id="noContainerSelect"
                                                                    name="no_container"
                                                                    data-placeholder="--- Pilih No Container ---"
                                                                    data-ajax-url="{{ route('gateout.select2') }}"
                                                                    data-dropdown-parent="#default"
                                                                    data-old='@json(old('no_container'))'>
                                                                    <option value=""></option>
                                                                </select>
                                                            </div>

                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- End Modal --}}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    {{-- jQuery --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    {{-- DataTables --}}
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    {{-- Select2 (pastikan CSS/JS juga di-include di layout atau di sini) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.full.min.js"></script>

    <script>
        $(function() {
            $('#table-gateout').DataTable({
                paging: true,
                pagingType: 'full_numbers',
                lengthMenu: [5, 10, 25, 50, 100],
            });
        });

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

                if (dp) {
                    opts.dropdownParent = $(dp);
                }

                $el.select2(opts);

                $el.on('select2:open', function() {
                    var s = document.querySelector('.select2-container--open .select2-search__field');
                    if (s) s.focus();
                });

                // Preselect old value saat validasi gagal
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
