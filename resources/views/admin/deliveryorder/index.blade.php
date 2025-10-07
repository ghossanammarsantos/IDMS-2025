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
                                    <h4 class="card-title text-white" id="basic-layout-form">Delivery Order</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1"
                                                    data-toggle="modal" data-target="#addNew"><i class="fa fa-plus"></i>
                                                    add New</button>
                                            </div>
                                        </div>
                                        <!-- <button type="button" class="btn btn-info btn-min-width mr-1 mb-1">Add New</button> -->
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Tanggal DO</th>
                                                        <th>Lokasi</th>
                                                        <th>Service</th>
                                                        <th>DO Number</th>
                                                        <th>Shipping Line</th>
                                                        <th>Sub-Owner</th>
                                                        <th>DO Expired</th>
                                                        <th>Vessel</th>
                                                        <th>Voyage</th>
                                                        <th>POL</th>
                                                        <th>POD</th>
                                                        <th>Commodity</th>
                                                        <th>Shipper</th>
                                                        <th>On Behalf</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($deliveryorder_list as $data)
                                                        <tr>
                                                            <td>{{ $data->tgl_wo }}</td>
                                                            <td>{{ $data->lokasi }}</td>
                                                            <td>{{ $data->service }}</td>
                                                            <td>{{ $data->do_number }}</td>
                                                            <td>{{ $data->shipping_line }}</td>
                                                            <td>{{ $data->sub_owner }}</td>
                                                            <td>{{ $data->do_expired }}</td>
                                                            <td>{{ $data->vessel }}</td>
                                                            <td>{{ $data->voyage }}</td>
                                                            <td>{{ $data->pol }}</td>
                                                            <td>{{ $data->pod }}</td>
                                                            <td>{{ $data->commodity }}</td>
                                                            <td>{{ $data->shipper }}</td>
                                                            <td>{{ $data->on_behalf }}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    data-toggle="modal" data-target="#editModal"
                                                                    data-no_container="{{ $data->no_container }}">Edit</button>
                                                                <form
                                                                    action="{{ route('deliveryorder.destroy', $data->no_container) }}"
                                                                    method="POST" style="display:inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger btn-sm">Hapus</button>
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
                                        <div class="modal fade text-left" id="addNew" tabindex="-1" role="dialog"
                                            aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-dark" style="border-radius:0px;">
                                                        <h4 class="modal-title text-white" id="myModalLabel1">Add New
                                                            Announcement Import</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form class="form" action="{{ route('deliveryorder.store') }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="form-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="tanggal_wo">Tanggal DO</label>
                                                                            <input type="date" id="tanggal_do"
                                                                                class="form-control" placeholder=""
                                                                                name="tgl_wo">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="depo">Lokasi</label>
                                                                            <select
                                                                                class="single-select-box selectivity-input"
                                                                                id="depo"
                                                                                data-placeholder="Pilih lokasi depo"
                                                                                name="lokasi">
                                                                                <option value="">-- Pilih Lokasi --
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="service">Service</label>
                                                                            <select
                                                                                class="single-select-box selectivity-input"
                                                                                id="service"
                                                                                data-placeholder="Pilih Service"
                                                                                name="service">
                                                                                <option value="">-- Pilih Service --
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="do_number">DO Number</label>
                                                                            <input type="text" id="do_number"
                                                                                class="form-control" placeholder=""
                                                                                name="do_number">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="shipping_line">Shipping
                                                                                Line</label>
                                                                            <select
                                                                                class="single-select-box selectivity-input"
                                                                                id="shipping_line"
                                                                                data-placeholder="Pilih Shipping Line"
                                                                                name="shipping_line">
                                                                                <option value="">-- Pilih Shipping
                                                                                    Line --</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="sub_owner">Sub-Owner</label>
                                                                            <input type="text" id="sub_owner"
                                                                                class="form-control" placeholder=""
                                                                                name="sub_owner">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="do_expired">DO Expired</label>
                                                                            <input type="date" id="do_expired"
                                                                                class="form-control" placeholder=""
                                                                                name="do_expired">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="vessel">Vessel</label>
                                                                            <input type="text" id="vessel"
                                                                                class="form-control" placeholder=""
                                                                                name="vessel">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="voyage">Voyage</label>
                                                                            <input type="text" id="voyage"
                                                                                class="form-control" placeholder=""
                                                                                name="voyage">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="pol">POL</label>
                                                                            <input type="text" id="pol"
                                                                                class="form-control" placeholder=""
                                                                                name="pol">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="pod">POD</label>
                                                                            <input type="text" id="pod"
                                                                                class="form-control" placeholder=""
                                                                                name="pod">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="commodity">Commodity</label>
                                                                            <input type="text" id="commodity"
                                                                                class="form-control" placeholder=""
                                                                                name="commodity">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="shipper">Shipper</label>
                                                                            <input type="text" id="shipper"
                                                                                class="form-control" placeholder=""
                                                                                name="shipper">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="on_behalf">On Behalf</label>
                                                                            <input type="text" id="on_behalf"
                                                                                class="form-control" placeholder=""
                                                                                name="on_behalf">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    changes</button>
                                                            </div>
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

    {{-- <script>
        $(document).ready(function() {
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var no_container = button.data('no_container');
                var modal = $(this);
                modal.find('.modal-body #edit_annimport_id').val(no_container);
            });
        });
    </script> --}}
@endsection
