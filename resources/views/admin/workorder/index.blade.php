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
                                    <h4 class="card-title text-white" id="basic-layout-form">Work Order</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <a href="{{ route('workorder.create') }}"
                                                        class="btn btn-dark btn-min-width mr-1 mb-1">
                                                        <i class="fa fa-plus"></i> Add New
                                                    </a>
                                            </div>
                                            <div class="col-md-2">
                                                <select class="single-select-box selectivity-input" id="single-select-box"
                                                    data-placeholder="No customer category selected"
                                                    name="customer_category"
                                                    style="padding-left:10px;border:1px solid silver;">
                                                    <option value="">Chooose Status</option>
                                                    <option value="">OPEN</option>
                                                    <option value="">NOTA</option>
                                                    <option value="">INVO</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="date" class="form-control">
                                            </div>
                                        </div>
                                        <!-- <button type="button" class="btn btn-info btn-min-width mr-1 mb-1">Add New</button> -->
                                        <div class="table-responsive">
                                            <table id="table-wo" class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Work Order</th>
                                                        <th>Tanggal Work Order</th>
                                                        <th>Customer</th>
                                                        <th>Kapal</th>
                                                        <th>No Do</th>
                                                        <th>Voyage</th>
                                                        <th>Shipper</th>
                                                        <th>Lokasi</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($workorder_list as $data)
                                                        <tr>
                                                            <td>{{ $data->nomor_wo }}</td>
                                                            <td>{{ $data->set_time }}</td>
                                                            <td>{{ isset($data->nama_customer) ? $data->nama_customer : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->nama_kapal) ? $data->nama_kapal : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->no_do) ? $data->no_do : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->voyage) ? $data->voyage : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->shipper) ? $data->shipper : '-' }}
                                                            </td>
                                                            <td>{{ isset($data->nama_gudang) ? $data->nama_gudang : '-' }}
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-blue btn-sm"
                                                                    onclick="addContainer('{{ $data->nomor_wo }}')"
                                                                    data-toggle="modal" data-target="#addContainerModal"><i
                                                                        class="fa fa-plus"></i> Tambah Container</button>
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    onclick="viewContainerDetail('{{ $data->nomor_wo }}')"
                                                                    data-toggle="modal"
                                                                    data-target="#containerDetailModal"><i
                                                                        class="fa fa-eye"></i> Detail Container</button>
                                                                <button class="btn btn-black btn-sm">
                                                                        <a class="fa fa-print" href="{{ route('workorder.cetak_wo', ['nomor_wo' => $data->nomor_wo]) }}">
                                                                            Cetak WO
                                                                        </a>
                                                                </button>
                                                            </td>
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
                                                        <h4 class="modal-title text-white" id="myModalLabel1">Add New Work
                                                            Order</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <form class="form" action="{{ route('workorder.store') }}" method="POST">
                                                            @csrf
                                                            <div class="form-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="customer">Customer</label>
                                                                            <select class="single-select-box selectivity-input" id="customer" data-placeholder="No customer selected" name="nama_customer" style="padding-left:10px;border:1px solid silver;">
                                                                                <option value="">-- Pilih Customer --</option>
                                                                                @foreach ($customer as $row)
                                                                                <option value="{{ $row->nama_customer }}">{{ $row->nama_customer }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="kapal">Kapal</label>
                                                                            <select class="single-select-box selectivity-input" id="kapal" data-placeholder="No kapal selected" name="nama_kapal" style="padding-left:10px;border:1px solid silver;">
                                                                                <option value="">-- Pilih Kapal --</option>
                                                                                @foreach ($kapal as $row)
                                                                                <option value="{{ $row->nama_kapal }}">{{ $row->nama_kapal }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="gudang">Lokasi</label>
                                                                            <select class="single-select-box selectivity-input" id="gudang" data-placeholder="No lokasi selected" name="nama_gudang">
                                                                                <option value="">-- Pilih Lokasi --</option>
                                                                                @foreach ($gudang as $row)
                                                                                <option value="{{ $row->nama_gudang }}">{{ $row->nama_gudang }}</option>
                                                                                @endforeach
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

                                        <!-- Modal Tambah Container -->
                                        <div class="modal fade" id="addContainerModal" tabindex="-1" role="dialog"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Tambah Container ke
                                                            Work Order</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form id="addContainerForm"
                                                        action="{{ route('workorder.storedetail') }}" method="post">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-5">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control"
                                                                            name="nomor_wo" id="nomor_wo"
                                                                            value="" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <div class="form-group">
                                                                        <select class="select2 form-control"
                                                                            multiple="multiple" name="no_container[]"
                                                                            id="containerSelect" onchange="addCont()"
                                                                            style="width:100%;">
                                                                            @foreach ($containerlist as $rows)
                                                                                <option value="{{ $rows->no_container }}">
                                                                                    {{ $rows->no_container }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="table-responsive" style="margin-top: 10px;">
                                                                <table class="table table-hover table-bordered">
                                                                    <thead class="thead-dark">
                                                                        <tr>
                                                                            <th>No Container</th>
                                                                            <th>Ukuran Container</th>
                                                                            <th>Service</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="table_data">
                                                                        <tr>
                                                                            <td colspan="3">Empty</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn grey btn-outline-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save
                                                                changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal untuk Detail Container -->
                                        <div class="modal fade" id="containerDetailModal" tabindex="-1" role="dialog"
                                            aria-labelledby="containerDetailModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="containerDetailModalLabel">Detail
                                                            Container</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive" style="margin-top: 10px;">
                                                            <table class="table table-hover table-bordered text-center">
                                                                <thead class="thead-dark">
                                                                    <tr>
                                                                        <th>No Container</th>
                                                                        <th>Ukuran Container</th>
                                                                        <th>Service</th>
                                                                        <th>Tarif</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="containerDetailModalBody">
                                                                    <tr>
                                                                        <td colspan="3">Empty</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Tutup</button>
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
            $('#table-wo').DataTable({
                paging: true, 
                pagingType: 'full_numbers', 
                lengthMenu: [5, 10, 25, 50, 100], 
            });
            $('#containerSelect').on('select2:unselect', function (e) {
                $('#table_data').empty();
            });
        });
    </script>
    <script>
        function addContainer(no_workorder) {
            console.log(no_workorder);
            $('#nomor_wo').val(no_workorder);
            $("#containerSelect").val('').trigger("change");
            $('#table_data').empty();
        }

        

        function addCont() {
            var no_container = $('#containerSelect').val();
            // console.log(no_container);
            $.ajax({
                url: "workorder/" + no_container,
                method: "GET",
                dataType: "json",
                beforeSend: function() {

                },
                success: function(data) {
                    $('#table_data').empty();
                    console.log(data);
                    data.forEach(function(element, index, array) {
                        var html = '<tr>';
                        html += '<td>' + element[0].no_container + '</td>';
                        html += '<td>' + element[0].size_type + '</td>';
                        html += '<td>' + element[0].kegiatan + '</td>';
                        html += '</tr>';
                        $('#table_data').append(html);
                    });
                }
            });
        }

        function viewContainerDetail(no_workorder) {
            // console.log(no_workorder);
            $('#containerDetailModalBody').empty();
            $.ajax({
                url: 'workorderdetail/' + no_workorder,
                method: 'GET',
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    response.forEach(function(element, index, array) {
                        var html = '<tr>';
                        html += '<td>' + element[0].no_container + '</td>';
                        html += '<td>' + element[0].size_type + '</td>';
                        html += '<td>' + element[0].kegiatan + '</td>';
                        html += '<td>' + element[0].tarif + '</td>';
                        html += '</tr>';
                        $('#containerDetailModalBody').append(html);
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    </script>
@endsection
