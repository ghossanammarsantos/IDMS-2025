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
                                    <h4 class="card-title text-white" id="basic-layout-form">Add New work Order</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <form class="form" action="{{ route('workorder.store') }}" method="POST">
                                            @csrf
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="noContainerSelect">Shipping Line</label>
                                                            <select class="form-control" id="noContainerSelect" name="nama_customer">
                                                                <option value="">-- Pilih Shipping Line --</option>
                                                                    @foreach ($customer as $row)
                                                                    <option value="{{ $row->nama_customer }}">{{ $row->nama_customer }}</option>
                                                                    @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="noContainerSelect">Vessel</label>
                                                            <select class="form-control" id="noContainerSelect" name="nama_kapal">
                                                                <option value="">-- Pilih Vessel --</option>
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
                                                            <label for="no_do">No DO</label>
                                                            <input type="text" id="no_do" name="no_do" class="form-control" placeholder="Masukkan no do...">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="voyage">Voyage</label>
                                                            <input type="text" id="voyage" name="voyage" class="form-control" placeholder="Masukkan voyage...">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="shipper">Shipper</label>
                                                            <input type="text" id="shipper" name="shipper" class="form-control" placeholder="Masukkan shipper...">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="gudang">Lokasi</label>
                                                            <select class="form-control" id="gudang" data-placeholder="No lokasi selected" name="nama_gudang">
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
                                                <a href="{{ route('workorder.index') }}" class="btn grey btn-secondary">
                                                    Close
                                                </a>
                                                <button type="submit" class="btn btn-dark">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
